<?php

namespace App\Tests\Service;

use App\Entity\SmsMessage;
use App\Repository\SmsMessageRepository;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Lookups;

class TwilioServiceTest extends TestCase
{
    public function testTwilioServiceCreateSmsMessage(): void
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $twilioClientMock = $this->createTwilioClientMock();
        $smsMessageMock = $this->createMock(SmsMessage::class);
        $smsMessageMock->method('getRecipient')->willReturn('');
        $repositoryMock = $this->createMock(SmsMessageRepository::class);
        $repositoryMock->method('find')->willReturn($smsMessageMock);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($repositoryMock);

        $twilioService = new TwilioService($twilioClientMock, $parameterBagMock, $entityManagerMock);
        $response = $twilioService->createSmsMessage($smsMessageMock);

        $this->assertEquals($response->sid, 'someIdentifier');
    }

    public function testCreateSmsMessageUpdatesSmsMessageStatus(): void
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $twilioClientMock = $this->createTwilioClientMock();
        $smsMessageMock = $this->createMock(SmsMessage::class);
        $smsMessageMock->method('getRecipient')->willReturn('');
        $smsMessageMock->expects($this->once())->method('setStatus')->with('pending');
        $repositoryMock = $this->createMock(SmsMessageRepository::class);
        $repositoryMock->method('find')->willReturn($smsMessageMock);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($repositoryMock);
        $entityManagerMock->expects($this->once())->method('flush');

        $twilioService = new TwilioService($twilioClientMock, $parameterBagMock, $entityManagerMock);
        $response = $twilioService->createSmsMessage($smsMessageMock);

        $this->assertEquals($response->sid, 'someIdentifier');
    }

    public function testCreateSmsMessageRecordsSmsMessageError(): void
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $twilioClientMock = $this->createTwilioClientMock(true);
        $smsMessageMock = $this->createMock(SmsMessage::class);
        $smsMessageMock->method('getRecipient')->willReturn('');
        $smsMessageMock->expects($this->once())->method('setStatus')->with('error');
        $repositoryMock = $this->createMock(SmsMessageRepository::class);
        $repositoryMock->method('find')->willReturn($smsMessageMock);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($repositoryMock);

        $twilioService = new TwilioService($twilioClientMock, $parameterBagMock, $entityManagerMock);
        $twilioService->createSmsMessage($smsMessageMock);
    }

    public function testTwilioServiceformatPhoneNumber(): void
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $twilioClientMock = $this->createTwilioClientMock();
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $twilioService = new TwilioService($twilioClientMock, $parameterBagMock, $entityManagerMock);
        $formatted = $twilioService->formatPhoneNumber('07654321');

        $this->assertEquals('+447654321', $formatted);
    }

    protected function createTwilioClientMock(bool $triggerException = false): MockObject
    {
        $phoneNumberInstanceMock = $this->createMock(Lookups\V1\PhoneNumberContext::class);
        $phoneNumberInstanceMock->phoneNumber = '+447654321';

        $phoneNumberContextMock = $this->createMock(Lookups\V1\PhoneNumberContext::class);
        $phoneNumberContextMock->method('fetch')->willReturn($phoneNumberInstanceMock);

        $phoneNumberListMock = $this->createMock(Lookups\V1\PhoneNumberList::class);
        $phoneNumberListMock->method('getContext')->willReturn($phoneNumberContextMock);

        $lookupsV1Mock = $this->createMock(Lookups\V1::class);
        $lookupsV1Mock->phoneNumbers = $phoneNumberListMock;

        $lookupsMock = $this->createMock(Lookups::class);
        $lookupsMock->v1 = $lookupsV1Mock;

        $messageInstanceMock = $this->createMock(MessageInstance::class);
        $messageInstanceMock->sid = 'someIdentifier';
        $messageInstanceMock->status = 'pending';

        $messageListMock = $this->createMock(MessageList::class);
        if ($triggerException) {
            $messageListMock->method('create')->willThrowException(new TwilioException());
        } else {
            $messageListMock->method('create')->willReturn($messageInstanceMock);
        }

        $twilioClientMock = $this->createMock(Client::class);
        $twilioClientMock->messages = $messageListMock;
        $twilioClientMock->lookups = $lookupsMock;

        return $twilioClientMock;
    }
}
