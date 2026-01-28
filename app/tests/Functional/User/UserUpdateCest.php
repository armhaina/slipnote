<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Faker\Factory;

final class UserUpdateCest extends AbstractCest
{
    private const string URL = '/api/v1/users';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT: Изменить пользователя');

        $user = $this->authorized(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
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

        $user = $this->authorized(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
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
        $user = UserFixtures::load(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId());
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
        $user = UserFixtures::load(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedEmailAlreadyExistsProvider')]
    public function failedEmailAlreadyExists(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT: Почта уже существует (ошибка)');

        $user = $this->authorized(I: $I);
        UserFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
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
                    'email' => 'update@mail.ru',
                ],
                'response' => [
                    'email' => 'update@mail.ru',
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
                    'email' => 'update@mail.ru',
                ],
                'request' => [
                    'email' => 'update@mail.ru',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Пользователь с почтой update@mail.ru уже существует',
                ],
            ],
        ];
    }
}
