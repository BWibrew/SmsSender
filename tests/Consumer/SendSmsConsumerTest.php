<?php

namespace App\Tests\Consumer;

use App\Consumer\SendSmsConsumer;
use App\Entity\SmsMessage;
use App\Service\TwilioService;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class SendSmsConsumerTest extends TestCase
{
    public function testSendSmsConsumerSubmitsTwilioSmsMessage(): void
    {
        $smsMessage = new SmsMessage();

        $serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serializerMock->method('deserialize')->willReturn($smsMessage);

        $twilioServiceMock = $this->getMockBuilder(TwilioService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $twilioServiceMock->expects($this->once())->method('createSmsMessage')->with($smsMessage);
        $consumer = new SendSmsConsumer($twilioServiceMock, $serializerMock);

        $amqpMessageMock = $this->getMockBuilder(AMQPMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $amqpMessageMock->method('getBody')->willReturn(serialize($smsMessage));
        $consumer->execute($amqpMessageMock);
    }
}
