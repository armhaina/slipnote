<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\Entity\Note;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\NoteFixture;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\Test\TestFailedAuthorizationTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedForbiddenTrait;
use App\Tests\Support\Data\Trait\Test\TestSuccessTrait;
use App\Tests\Support\FunctionalTester;
use Symfony\Component\HttpFoundation\Request;

final class NoteGetCest extends AbstractCest
{
    use TestSuccessTrait;
    use TestFailedAuthorizationTrait;
    use TestFailedForbiddenTrait;

    private const string URL = '/api/v1/notes';

    protected static function getMethod(): string
    {
        return Request::METHOD_GET;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        return self::URL.'/'.self::getEntity(I: $I, fixtures: $context['fixture'] ?? [])->getId();
    }

    protected function successProvider(): array
    {
        return [
            [
                'context' => [
                    'fixture' => [
                        'name' => 'Заметка_0',
                        'description' => 'Описание_0',
                        'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'is_trashed' => false,
                    'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                ],
            ],
        ];
    }

    private static function getEntity(FunctionalTester $I, array $fixtures = []): Note
    {
        return NoteFixture::load(I: $I, data: $fixtures);
    }
}
