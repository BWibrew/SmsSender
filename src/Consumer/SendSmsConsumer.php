<?php

namespace App\Consumer;

use App\Entity\SmsMessage;
use App\Service\TwilioService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

class SendSmsConsumer implements ConsumerInterface
{
    /**
     * @var TwilioService
     */
    protected $twilio;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(TwilioService $twilio, SerializerInterface $serializer)
    {
        $this->twilio = $twilio;
        $this->serializer = $serializer;
    }

    public function execute(AMQPMessage $message)
    {
        /** @var SmsMessage $smsMessage */
        $smsMessage = $this->serializer->deserialize($message->getBody(), SmsMessage::class, 'json');

        $this->twilio->createSmsMessage($smsMessage);
    }
}
