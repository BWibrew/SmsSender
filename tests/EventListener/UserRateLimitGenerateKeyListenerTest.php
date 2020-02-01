<?php

namespace App\Tests;

use App\EventListener\UserRateLimitGenerateKeyListener;
use Noxlogic\RateLimitBundle\Events\GenerateKeyEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRateLimitGenerateKeyListenerTest extends TestCase
{
    public function testUserRateLimitGenerateKeyListenerSetsKeyForGuest(): void
    {
        $securityMock = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock();
        $securityMock->method('getUser')->willReturn(null);
        $listener = new UserRateLimitGenerateKeyListener($securityMock);

        $generateKeyEventMock = $this->getMockBuilder(GenerateKeyEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $generateKeyEventMock->expects($this->once())->method('addToKey')->with('Guest');
        $listener->onGenerateKey($generateKeyEventMock);
    }

    public function testUserRateLimitGenerateKeyListenerSetsKeyForUser(): void
    {
        $userMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $userMock->method('getUsername')->willReturn('Username');

        $securityMock = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock();
        $securityMock->method('getUser')->willReturn($userMock);

        $listener = new UserRateLimitGenerateKeyListener($securityMock);

        $generateKeyEventMock = $this->getMockBuilder(GenerateKeyEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $generateKeyEventMock->expects($this->once())->method('addToKey')->with('Username');
        $listener->onGenerateKey($generateKeyEventMock);
    }
}
