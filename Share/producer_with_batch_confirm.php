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


$queueName = 'test-single-queue';
$rabbit->createQueue($queueName, false, true, false, false);

//每次都ack性能很低，批量ack，设置每50个message集中ack
$batchSize               = 50;
$outstandingMessageCount = 0;
for ($i = 0; $i < 10000; $i++) {
    $message = $i . "this is a test message.";
    $rabbit->sendMessage($message, $queueName, '', [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT //消息持久化，重启rabbitmq，消息不会丢失
    ]);
    echo $message . '  已发送' . PHP_EOL;
    if (++$outstandingMessageCount == $batchSize) {
        echo '------';
        $channel->wait_for_pending_acks_returns(5);
        $outstandingMessageCount = 0;
    }
    sleep(1);
}
if ($outstandingMessageCount > 0) {
    $channel->wait_for_pending_acks_returns(5);
}

unset($rabbit);//关闭连接
