# php使用RabbitMQ的example

# 加载rabbitmq的composer包
composer require php-amqplib

# 目录说明
## Alone 目录
实现多个独立消费者
exchange的type=direct/topic

## Delay 目录
利用message的ttl、死信特性来实现延时队列

## Share 目录
多个消费者同时消费一个queue
