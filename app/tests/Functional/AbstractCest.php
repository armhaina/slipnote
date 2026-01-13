<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\UserAuthorizedFixtures;
use App\Tests\Support\FunctionalTester;

abstract class AbstractCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }

    protected function fixturesLoad(FunctionalTester $I, array $groups): void
    {
        $this->commandDoctrineFixturesLoad(I: $I, groups: $groups);
    }

    protected function authorized(FunctionalTester $I): void
    {
        $this->commandDoctrineFixturesLoad(I: $I, groups: UserAuthorizedFixtures::GROUPS);

        $I->sendPost(
            url: '/api/login_check',
            params: [
                'username' => UserAuthorizedFixtures::EMAIL,
                'password' => UserAuthorizedFixtures::PASSWORD,
            ]
        );
        $I->seeResponseCodeIs(code: 200);
        $I->seeResponseIsJson();

        $I->haveHttpHeader(
            name: 'Authorization',
            value: 'Bearer '.json_decode($I->grabResponse(), true)['token']
        );
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

    private function commandDoctrineFixturesLoad(FunctionalTester $I, array $groups = []): void
    {
        $I->runSymfonyConsoleCommand(
            command: 'doctrine:fixtures:load',
            parameters: [
                '--no-interaction' => '--no-interaction',
                '--purge-with-truncate' => true,
                '--group' => $groups,
                '--env' => 'test',
                '--append' => null,
            ]
        );
    }
}
