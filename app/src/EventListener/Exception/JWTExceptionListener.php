<?php

declare(strict_types=1);

namespace App\EventListener\Exception;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

readonly class JWTExceptionListener extends AbstractExceptionListener
{
    /**
     * @throws ExceptionInterface
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();

        $status = Response::HTTP_UNAUTHORIZED;

        $model = $this->exceptionFactory(exception: $exception, status: $status);
        $data = $this->serialize(model: $model);

        $event->setResponse(
            response: new JsonResponse(
                data: $data,
                status: $status,
                json: true
            )
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $exception = $event->getException();

        $status = Response::HTTP_UNAUTHORIZED;

        $model = $this->exceptionFactory(exception: $exception, status: $status);
        $data = $this->serialize(model: $model);

        $event->setResponse(
            response: new JsonResponse(
                data: $data,
                status: $status,
                json: true
            )
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $exception = $event->getException();

        $status = Response::HTTP_UNAUTHORIZED;

        $model = $this->exceptionFactory(exception: $exception, status: $status);
        $data = $this->serialize(model: $model);

        $event->setResponse(
            response: new JsonResponse(
                data: $data,
                status: $status,
                json: true
            )
        );
    }
}
