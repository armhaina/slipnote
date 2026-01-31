<?php

declare(strict_types=1);

namespace App\Tests\Support\Data\Trait;

use App\Tests\Support\FunctionalTester;
use Codeception\Scenario;
use Symfony\Component\HttpFoundation\Request;

trait AbstractTrait
{
    abstract protected static function getUrl(FunctionalTester $I, array $context = []): string;

    abstract protected static function getMethod(): string;

    protected static function setWantTo(Scenario $scenario, string $wantTo): void
    {
        $result = preg_replace(
            pattern: '/^.*?\s+\|\s+/',
            replacement: $wantTo.' | ',
            subject: $scenario->getFeature()
        );
        $scenario->setFeature(feature: $result);
    }

    protected static function except(array &$data, array $excludeKeys): array
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                self::except(data: $value, excludeKeys: $excludeKeys);
            }
        }

        foreach ($excludeKeys as $excludeKey) {
            if (isset($data[$excludeKey])) {
                unset($data[$excludeKey]);
            }
        }

        return $data;
    }

    protected function request(FunctionalTester $I, array $params = [], array $context = []): void
    {
        // TODO: переделать exception
        match (self::getMethod()) {
            Request::METHOD_GET => $I->sendGet(url: self::getUrl(I: $I, context: $context), params: $params),
            Request::METHOD_POST => $I->sendPost(url: self::getUrl(I: $I, context: $context), params: $params),
            Request::METHOD_PUT => $I->sendPut(url: self::getUrl(I: $I, context: $context), params: $params),
            Request::METHOD_DELETE => $I->sendDelete(url: self::getUrl(I: $I, context: $context)),
            default => throw new \Exception()
        };
    }
}
