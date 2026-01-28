<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Enum\Role;
use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Support\FunctionalTester;
use Codeception\Scenario;
use Codeception\Util\HttpCode;

abstract class AbstractCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }

    protected function authorized(FunctionalTester $I): User
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
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $I->haveHttpHeader(
            name: 'Authorization',
            value: 'Bearer '.json_decode($I->grabResponse(), true)['token']
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

    protected static function setWantTo(Scenario $scenario, string $wantTo): void
    {
        $result = preg_replace('/^.*?\s+\|\s+/', $wantTo.' | ', $scenario->getFeature());
        $scenario->setFeature($result);
    }
}
