<?php

declare(strict_types=1);

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

readonly class JWTExpiredListener
{
    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $test = 1;

        /** @var JWTAuthenticationFailureResponse */
        $response = $event->getResponse();

        $response->setMessage('Your token is expired, please renew it.');
    }
}
