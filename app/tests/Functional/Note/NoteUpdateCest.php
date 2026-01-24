<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\Tests\_data\fixtures\NoteFixtures;
use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Faker\Factory;

final class NoteUpdateCest extends AbstractCest
{
    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT: Изменить заметку');

        $this->authorized(I: $I);
        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendPut(url: '/api/v1/notes/' . $note->getId(), params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedValidationProvider')]
    public function failedValidation(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT: Ошибка валидации');

        $this->authorized(I: $I);
        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendPut(url: '/api/v1/notes/' . $note->getId(), params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedAuthorizationProvider')]
    public function failedAuthorization(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT: Ошибка авторизации');

        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendPut(url: '/api/v1/notes/' . $note->getId(), params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('forbiddenProvider')]
    public function forbidden(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT: Доступ запрещен');

        $this->authorized(I: $I);
        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendPut(url: '/api/v1/notes/' . $note->getId(), params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function mainProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
                'request' => [
                    'name' => 'Заметка_1',
                    'description' => 'Описание заметки_1',
                ],
                'response' => [
                    'name' => 'Заметка_1',
                    'description' => 'Описание заметки_1',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
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
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
                'request' => [
                    'name' => $faker->regexify('[A-Za-z0-9]{' . mt_rand(101, 101) . '}'),
                    'description' => $faker->regexify('[A-Za-z0-9]{' . mt_rand(10001, 10001) . '}')
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'name',
                            'message' => 'Название должно содержать максимум 100 символов'
                        ],
                        [
                            'property' => 'description',
                            'message' => 'Описание должно содержать максимум 10000 символов'
                        ]
                    ],
                ],
            ],
        ];
    }

    protected function failedAuthorizationProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
                'request' => [
                    'name' => 'Заметка_1',
                    'description' => 'Описание заметки_1',
                ],
                'response' => [
                    'code' => 401,
                    'message' => 'JWT Token not found',
                ],
            ],
        ];
    }

    protected function forbiddenProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => 'test_0@mail.ru'],
                ],
                'request' => [
                    'name' => 'Заметка_1',
                    'description' => 'Описание заметки_1',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Доступ запрещен',
                ],
            ],
        ];
    }
}
