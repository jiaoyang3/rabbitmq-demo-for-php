<?php

require_once '../../vendor/autoload.php';

use RabbitMQ\RabbitMQ;

$rabbit = new RabbitMQ();

$exchangeName = 'test-ex-topic';
$queueName    = 'test-consumer-ex-topic';
$routingKey   = 'test.ex.*';//消费规则定义

//创建队列
$rabbit->createQueue($queueName, false, true);
//绑定到交换机
$rabbit->bindQueue($queueName, $exchangeName, $routingKey);
//消费
$callback = function ($message) {
    var_dump("Received Message : " . $message->body);//print message
    sleep(2);//处理耗时任务
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);//ack
};
$rabbit->consumeMessage($queueName, $callback);

unset($rabbit);//关闭连接
