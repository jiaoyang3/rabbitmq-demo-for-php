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
    public function consumeMessage($queueName,$callback)
    {
        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);
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
