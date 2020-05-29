<?php
require_once '../../vendor/autoload.php';

// 生产者

$delay = new \RabbitMQ\Delay\DelayQueue();

$ttl            = 1000 * 100;//订单100s后超时
$delayExName    = 'delay-order-exchange';//超时exchange
$delayQueueName = 'delay-order-queue';//超时queue
$queueName      = 'ttl-order-queue';//订单queue

$delay->createQueue($ttl, $delayExName, $delayQueueName, $queueName);

//100个订单信息，每个订单超时时间都是10s
for ($i = 0; $i < 100; $i++) {
    $data = [
        'order_id' => $i + 1,
        'remark'   => 'this is a order test'
    ];
    $delay->sendMessage(json_encode($data), $queueName);
    sleep(1);
}


