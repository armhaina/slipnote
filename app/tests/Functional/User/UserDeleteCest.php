<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

final class UserDeleteCest extends AbstractCest
{
    private const string URL = '/api/v1/users';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('PUT/200: удалить пользователя');

        $user = $this->authorized(I: $I);

        $I->sendDelete(url: self::URL.'/'.$user->getId());
        $I->seeResponseCodeIs(code: HttpCode::OK);
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

        $I->sendDelete(url: self::URL.'/'.$user->getId());
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

        $I->sendDelete(url: self::URL.'/'.$user->getId());
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
                'response' => [
                    'success' => true,
                    'message' => 'Запись успешно удалена',
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
}
