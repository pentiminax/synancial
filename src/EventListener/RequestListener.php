<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Translation\LocaleSwitcher;

class RequestListener
{
    public function __construct(
        private readonly LocaleSwitcher $localeSwitcher,
        private readonly Security       $security
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();

        if ($user === null) {
            return;
        }

        $this->localeSwitcher->setLocale($user->getLanguage()->getCode());
    }
}