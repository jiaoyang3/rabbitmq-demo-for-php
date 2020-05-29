<?php

require_once '../../vendor/autoload.php';

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use RabbitMQ\RabbitMQ;


$rabbit = new RabbitMQ();

$routingKey1  = 'test.ex.queue1';
$routingKey2  = 'test.ex.queue2';
$exchangeName = 'test-ex-topic';
$rabbit->createExchange($exchangeName, AMQPExchangeType::TOPIC, false, true, false);

//向交换机和routingkey = test-ex-queue1中推送10000条数据
for ($i = 0; $i < 10000; $i++) {
    $rabbit->sendMessage($i . "this is a queue1 message.", $routingKey1, $exchangeName, [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT //消息持久化，重启rabbitmq，消息不会丢失
    ]);
}
//向交换机和routingkey = test-ex-queue2中推送10000条数据
for ($i = 0; $i < 10000; $i++) {
    $rabbit->sendMessage($i . "this is a queue2 message.", $routingKey2, $exchangeName, [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT //消息持久化，重启rabbitmq，消息不会丢失
    ]);
}

unset($rabbit);//关闭连接
