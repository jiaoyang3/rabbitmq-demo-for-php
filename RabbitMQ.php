<?php


namespace RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ
{

    private $host = '127.0.0.1';
    private $port = 5672;
    private $user = 'guest';
    private $password = 'guest';
    protected $connection;
    protected $channel;


    /**
     * RabbitMQ constructor.
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
        $this->channel    = $this->connection->channel();
    }

    /**
     * @param $exchangeName
     * @param $type
     * @param $pasive
     * @param $durable
     * @param $autoDelete
     */
    public function createExchange($exchangeName, $type, $pasive = false, $durable = false, $autoDelete = false)
    {
        $this->channel->exchange_declare($exchangeName, $type, $pasive, $durable, $autoDelete);
    }

    /**
     * @param $queueName
     * @param $pasive
     * @param $durable
     * @param $exlusive
     * @param $autoDelete
     */
    public function createQueue($queueName, $pasive = false, $durable = false, $exlusive = false, $autoDelete = false, $nowait = false, $arguments = [])
    {
        $this->channel->queue_declare($queueName, $pasive, $durable, $exlusive, $autoDelete, $nowait, $arguments);
    }

    /**
     * @param $exchangeName
     * @param $queue
     * @param string $routing_key
     * @param bool $nowait
     * @param array $arguments
     * @param null $ticket
     */
    public function bindQueue($queue, $exchangeName, $routing_key = '',
                              $nowait = false,
                              $arguments = array(),
                              $ticket = null)
    {
        $this->channel->queue_bind($queue, $exchangeName, $routing_key, $nowait, $arguments, $ticket);
    }

    /**
     * 生成信息
     * @param $message
     */
    public function sendMessage($message, $routeKey, $exchange = '', $properties = [])
    {
        $data = new AMQPMessage(
            $message, $properties
        );
        $this->channel->basic_publish($data, $exchange, $routeKey);
    }

    /**
     * 消费消息
     * @param $queueName
     * @param $callback
     * @throws \ErrorException
     */
    public function consumeMessage($queueName, $callback, $tag = '', $noLocal = false, $noAck = false, $exclusive = false, $noWait = false)
    {
        //只有consumer已经处理并确认了上一条message时queue才分派新的message给它
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queueName, $tag, $noLocal, $noAck, $exclusive, $noWait, $callback);
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
