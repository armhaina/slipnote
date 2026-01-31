<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\UserFixtures;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Faker\Factory;

final class NoteCreateCest extends AbstractCest
{
    private const string URL = '/api/v1/notes';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/200: Создать заметку');

        $this->authorized(I: $I);

        $I->sendPost(url: self::URL, params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedAuthorizationProvider')]
    public function failedAuthorization(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/401: Ошибка авторизации');

        $I->sendPost(url: self::URL, params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedValidationProvider')]
    public function failedValidation(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/422: Ошибка валидации');

        $this->authorized(I: $I);

        $I->sendPost(url: self::URL, params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function mainProvider(): array
    {
        return [
            [
                'request' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                ],
                'response' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                    'is_trashed' => false,
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

    protected function failedAuthorizationProvider(): array
    {
        return [
            [
                'request' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                ],
                'response' => [
                    'code' => 401,
                    'message' => 'JWT Token not found',
                ],
            ],
        ];
    }
}
