<?php

require_once '../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use RabbitMQ\RabbitMQ;

try {
    $rabbit  = new RabbitMQ();
    $channel = $rabbit->getChannel();
    $channel->tx_select();//begin trx

    $queueName = 'test-single-queue2';
    $rabbit->createQueue($queueName, false, true, false, false);

    for ($i = 0; $i < 10000; $i++) {
        $rabbit->sendMessage($i . "this is a test message.", $queueName, '', [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT //消息持久化，重启rabbitmq，消息不会丢失
        ]);
        if ($i == 10) {
            throw new Exception('rollbock');
        }
    }
    $channel->tx_commit();//commit trx
    unset($rabbit);//close
} catch (Exception $e) {
    $channel->tx_rollback();//rollback
    echo $e->getMessage();
}
