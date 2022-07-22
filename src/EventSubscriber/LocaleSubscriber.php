<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => [
                ['fromAcceptLanguageHeader', 96],
                ['fromUserSession', 24],
            ]
        ];
    }

    public function fromAcceptLanguageHeader(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $preferred = $request->getPreferredLanguage(['en', 'fr']);

        if ($preferred) {
            $request->setLocale($preferred);
        }
    }

    public function fromUserSession(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $sessionLocale = $request->getSession()->get('user.locale');

        if ($sessionLocale) {
            $request->setLocale($sessionLocale);
        }
    }
}
