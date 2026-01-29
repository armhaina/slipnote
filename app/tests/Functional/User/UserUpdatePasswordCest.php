<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;
use Faker\Factory;

final class UserUpdatePasswordCest extends AbstractCest
{
    private const string URL = '/api/v1/users';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT/200: Изменить пароль пользователя');

        $user = $this->authorized(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId().'/password', params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedInvalidCurrentPasswordProvider')]
    public function failedInvalidCurrentPassword(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT/400: Неверный текущий пароль пользователя');

        $user = $this->authorized(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId().'/password', params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedAuthorizationProvider')]
    public function failedAuthorization(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT/401: Ошибка авторизации');

        $user = UserFixtures::load(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId().'/password');
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('forbiddenProvider')]
    public function forbidden(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT/403: Доступ запрещен');

        $this->authorized(I: $I);
        $user = UserFixtures::load(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId().'/password', params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedValidationPasswordMinProvider')]
    public function failedValidationPasswordMin(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/422: Ошибка валидации (пароль минимум)');

        $user = $this->authorized(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId().'/password', params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedValidationPasswordMaxProvider')]
    public function failedValidationPasswordMax(FunctionalTester $I, Example $example, Scenario $scenario): void
    {
        $I->wantTo('POST/422: Ошибка валидации (пароль максимум)');

        $user = $this->authorized(I: $I);

        $I->sendPut(url: self::URL.'/'.$user->getId().'/password', params: $example['request']);
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
                    'current_password' => UserFixtures::USER_AUTHORIZED_PASSWORD,
                    'new_password' => 'password123',
                ],
                'response' => [
                    'email' => UserFixtures::USER_AUTHORIZED_EMAIL,
                ],
            ],
        ];
    }

    protected function failedInvalidCurrentPasswordProvider(): array
    {
        return [
            [
                'request' => [
                    'current_password' => 'invalidate_password',
                    'new_password' => 'password123',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Неверный текущий пароль пользователя',
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
                    'current_password' => UserFixtures::USER_AUTHORIZED_PASSWORD,
                    'new_password' => 'password123',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Доступ запрещен',
                ],
            ],
        ];
    }

    protected function failedValidationPasswordMinProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                'request' => [
                    'current_password' => UserFixtures::USER_AUTHORIZED_PASSWORD,
                    'new_password' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(5, 5).'}'),
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'new_password',
                            'message' => 'Пароль должен содержать минимум 6 символов',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function failedValidationPasswordMaxProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                'request' => [
                    'current_password' => UserFixtures::USER_AUTHORIZED_PASSWORD,
                    'new_password' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(19, 19).'}'),
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'new_password',
                            'message' => 'Пароль должен содержать максимум 18 символов',
                        ],
                    ],
                ],
            ],
        ];
    }
}
