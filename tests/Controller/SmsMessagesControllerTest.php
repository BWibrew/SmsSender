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
            'sms_message[recipient]' => '123456',
            'sms_message[body]' => 'Hello World!',
        ]);

        $this->assertResponseRedirects('/');
    }
}
