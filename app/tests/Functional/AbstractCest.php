<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Contract\EntityInterface;
use App\DataFixtures\UserAuthorizedFixtures;
use App\Entity\User;
use App\Enum\Role;
use App\Tests\Support\FunctionalTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractCest
{
    public const USER_EMAIL = 'userAuthorized@mail.ru';
    public const USER_PASSWORD = 'userAuthorizedPassword';

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
            value: 'Bearer ' . json_decode($I->grabResponse(), true)['token']
        );
    }

    protected function fixturesLoadUpdate(FunctionalTester $I, string $entityClass, array $data): EntityInterface|string
    {
        $id = $I->haveInRepository(classNameOrInstance: $entityClass, data: $data);

        return $I->grabEntityFromRepository(entity: $entityClass, params: ['id' => $id]);
    }

    protected function authorizedUpdate(FunctionalTester $I): EntityInterface
    {
        $passwordHasher = $I->grabService(serviceId: UserPasswordHasherInterface::class);

        $user = $this->fixturesLoadUpdate(I: $I, entityClass: User::class, data: [
            'email' => self::USER_EMAIL,
            'password' => $passwordHasher->hashPassword(new User(), self::USER_PASSWORD),
            'roles' => [Role::ROLE_USER->value],
        ]);

        $I->sendPost(
            url: '/api/login_check',
            params: [
                'username' => self::USER_EMAIL,
                'password' => self::USER_PASSWORD,
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
