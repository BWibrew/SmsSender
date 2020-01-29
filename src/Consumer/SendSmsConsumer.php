<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class SendSmsConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
    }
}
