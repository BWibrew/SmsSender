<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmsMessagesControllerTest extends WebTestCase
{
    public function testDisplaysIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.card-header', '01792548317');
    }

    public function testDisplaysCreateNew(): void
    {
        $client = static::createClient();
        $client->request('GET', '/create');

        $this->assertResponseIsSuccessful();
    }

    public function testRedirectsToIndexAfterCreate(): void
    {
        $client = static::createClient();
        $client->request('GET', '/create');

        $client->submitForm('Send', [
            'sms_message[recipient]' => '01792548317',
            'sms_message[body]' => 'Hello World!',
        ]);

        $this->assertResponseRedirects('/');
    }

    public function testTwilioCallbackUpdatesStatus(): void
    {
        $client = static::createClient();
        $client->request('POST', '/twilio-callback', [
            'MessageStatus' => 'sent',
        ]);

        $this->assertResponseIsSuccessful();
    }
}
