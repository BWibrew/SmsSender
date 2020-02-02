<?php

namespace App\EventListener;

use Noxlogic\RateLimitBundle\Events\GenerateKeyEvent;
use Symfony\Component\Security\Core\Security;

class UserRateLimitGenerateKeyListener
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onGenerateKey(GenerateKeyEvent $event): void
    {
        $userName = $this->security->getUser() ? $this->security->getUser()->getUsername() : 'Guest';

        $event->addToKey($userName);
    }
}
