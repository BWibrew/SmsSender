<?php

namespace App\Tests\Service;

use App\Entity\SmsMessage;
use App\Service\TwilioService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

        $twilioService = new TwilioService($twilioClientMock, $parameterBagMock);
        $response = $twilioService->createSmsMessage($smsMessageMock);

        $this->assertEquals($response->sid, 'someIdentifier');
    }

    public function testTwilioServiceformatPhoneNumber(): void
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $twilioClientMock = $this->createTwilioClientMock();

        $twilioService = new TwilioService($twilioClientMock, $parameterBagMock);
        $formatted = $twilioService->formatPhoneNumber('07654321');

        $this->assertEquals('+447654321', $formatted);
    }

    protected function createTwilioClientMock(): MockObject
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

        $messageListMock = $this->createMock(MessageList::class);
        $messageListMock->method('create')->willReturn($messageInstanceMock);

        $twilioClientMock = $this->createMock(Client::class);
        $twilioClientMock->messages = $messageListMock;
        $twilioClientMock->lookups = $lookupsMock;

        return $twilioClientMock;
    }
}
