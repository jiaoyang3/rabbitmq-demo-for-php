<?php

require_once '../../vendor/autoload.php';

use RabbitMQ\RabbitMQ;

$rabbit = new RabbitMQ();

$queueName = 'test-single-queue';
$callback = function ($message){
    var_dump("Received Message : " . $message->body);//print message
    sleep(2);//处理耗时任务
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);//ack
};
$rabbit->consumeMessage($queueName,$callback);

unset($rabbit);//关闭连接
