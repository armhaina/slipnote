<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

final class UserCreateCest extends AbstractCest
{
    private const string URL = '/api/v1/users';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/200: Создать пользователя');

        $I->sendPost(url: self::URL, params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedEmailAlreadyExistsProvider')]
    public function failedEmailAlreadyExists(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/409: Почта уже существует');

        UserFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendPut(url: self::URL, params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedValidationProvider')]
    public function failedValidation(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/422: Ошибка валидации');

        $I->sendPut(url: self::URL, params: $example['request']);
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
                    'email' => 'create@mail.ru',
                ],
                'response' => [
                    'email' => 'create@mail.ru',
                ],
            ],
        ];
    }

    protected function failedValidationProvider(): array
    {
        return [
            [
                'request' => [
                    'email' => 'test',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'email',
                            'message' => 'Email не соответствует формату электронной почты',
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
                'request' => [
                    'email' => 'update@mail.ru',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Доступ запрещен',
                ],
            ],
        ];
    }

    protected function failedEmailAlreadyExistsProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'email' => 'create@mail.ru',
                ],
                'request' => [
                    'email' => 'create@mail.ru',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Пользователь с почтой create@mail.ru уже существует',
                ],
            ],
        ];
    }
}
