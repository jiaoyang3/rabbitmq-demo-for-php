<?php

require_once '../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use RabbitMQ\RabbitMQ;


$rabbit  = new RabbitMQ();
$channel = $rabbit->getChannel();
$channel->confirm_select();//open confirm
//ack callback function
$channel->set_ack_handler(function (AMQPMessage $message){
    echo 'ack' . $message->getBody() . PHP_EOL;
});
//nack callback function
$channel->set_nack_handler(function (AMQPMessage $message){
    echo 'ack' . $message->getBody() .PHP_EOL;
});

$queueName = 'test-single-queue1';
$rabbit->createQueue($queueName, false, true, false, false);

for ($i = 0; $i < 10000; $i++) {
    $message = $i . "this is a test message.";
    $rabbit->sendMessage($message, $queueName, '', [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);
    echo $message . '  has been sent' . PHP_EOL;
    $channel->wait_for_pending_acks_returns(5);//set wait time
    sleep(1);
}

unset($rabbit);//关闭连接
