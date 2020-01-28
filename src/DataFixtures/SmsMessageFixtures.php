<?php

namespace App\DataFixtures;

use App\Entity\SmsMessage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SmsMessageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $smsMessage = new SmsMessage();
        $smsMessage->setRecipient('01792548317');
        $smsMessage->setBody('Hello World!');

        $manager->persist($smsMessage);
        $manager->flush();
    }
}
