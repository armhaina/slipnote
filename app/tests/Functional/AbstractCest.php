<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Contract\Entity\EntityInterface;
use App\Enum\Role;
use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Support\FunctionalTester;

abstract class AbstractCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }

    protected function authorized(FunctionalTester $I): EntityInterface
    {
        $user = UserFixtures::load(I: $I, data: [
            'email' => UserFixtures::USER_AUTHORIZED_EMAIL,
            'password' => UserFixtures::USER_AUTHORIZED_PASSWORD,
            'roles' => [Role::ROLE_USER->value],
        ]);

        $I->sendPost(
            url: '/api/login_check',
            params: [
                'username' => UserFixtures::USER_AUTHORIZED_EMAIL,
                'password' => UserFixtures::USER_AUTHORIZED_PASSWORD,
            ]
        );
        $I->seeResponseCodeIs(code: 200);
        $I->seeResponseIsJson();

        $I->haveHttpHeader(
            name: 'Authorization',
            value: 'Bearer ' . json_decode($I->grabResponse(), true)['token']
        );

        return $user;
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
}
