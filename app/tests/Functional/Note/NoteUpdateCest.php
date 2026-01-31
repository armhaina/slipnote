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
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;

final class NoteUpdateCest extends AbstractCest
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
        $id = self::getEntity(I: $I, fixtures: $context['fixtures'] ?? [])->getId();

        return self::URL.'/'.$id;
    }

    protected function successProvider(): array
    {
        return [
            [
                'want_to' => 'Изменить заметку',
                'is_authorize' => true,
                'context' => [
                    'params' => [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                    ],
                    'fixtures' => [
                        'name' => 'Заметка_0',
                        'description' => 'Описание_0',
                        'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'name' => 'Заметка_1',
                    'description' => 'Описание заметки_1',
                    'is_trashed' => false,
                    'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                ],
            ],
        ];
    }

    protected function failedValidationProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                ],
                'request' => [
                    'name' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(101, 101).'}'),
                    'description' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(10001, 10001).'}'),
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'name',
                            'message' => 'Название должно содержать максимум 100 символов',
                        ],
                        [
                            'property' => 'description',
                            'message' => 'Описание должно содержать максимум 10000 символов',
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function getEntity(FunctionalTester $I, array $fixtures = []): Note
    {
        return NoteFixture::load(I: $I, data: $fixtures);
    }
}
