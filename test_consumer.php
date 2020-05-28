<?php
require_once '../vendor/autoload.php';

// 消费者

$delay = new \RabbitMQ\DelayQueue();

$delayQueueName = 'delay-order-queue';

$callback = function ($msg) {
    echo $msg->body . PHP_EOL;
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

    //处理订单超时逻辑，给用户推送提醒等等。。。
    sleep(10);
};

/**
 * 消费已经超时的订单信息，进行处理
 */
$delay->consumeMessage($delayQueueName, $callback);


