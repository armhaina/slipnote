<?php

declare(strict_types=1);

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthenticationFailureListener
{
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $data = [
            'name' => 'John Doe',
            'foo' => 'bar',
        ];

        $response = new JWTAuthenticationFailureResponse(
            'Bad credentials, please verify that your username/password are correctly set',
            Response::HTTP_UNAUTHORIZED
        );
        $response->setData($data);

        $event->setResponse($response);
    }
}
