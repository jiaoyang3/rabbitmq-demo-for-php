# php使用RabbitMQ实现延时队列

# 加载rabbitmq的composer包
composer require php-amqplib

# Alone 
实现多个独立消费者
exchange的type=direct/topic

# Delay
利用message的ttl、死信特性来实现延时队列

# Share
多个消费者同时消费一个queue
