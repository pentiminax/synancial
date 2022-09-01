<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Translation\LocaleSwitcher;

class UserLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LocaleSwitcher $localeSwitcher
    )
    {
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $user->getLanguage()->getCode()) {
            $this->localeSwitcher->setLocale($user->getLanguage()->getCode());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }
}