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

final class NoteTrashRestoreCest extends AbstractCest
{
    use TestSuccessTrait;
    use TestFailedAuthorizationTrait;
    use TestFailedForbiddenTrait;

    private const string URL = '/api/v1/notes';

    protected static function getMethod(): string
    {
        return Request::METHOD_PUT;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        $id = self::getEntity(I: $I, fixtures: $context['fixtures']['major'] ?? [])->getId();

        return self::URL.'/'.$id.'/trash/restore';
    }

    protected static function getEntity(FunctionalTester $I, array $fixtures = []): Note
    {
        return NoteFixture::load(I: $I, data: $fixtures);
    }

    protected function successProvider(): array
    {
        return [
            [
                'want_to' => 'Восстановить заметку из корзины',
                'is_authorize' => true,
                'context' => [
                    'fixtures' => [
                        'major' => [
                            'name' => 'Заметка_0',
                            'description' => 'Описание_0',
                            'is_trashed' => true,
                            'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                        ],
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
}
