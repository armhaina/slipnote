<?php

namespace App\Tests\_data\fixtures;

use App\Contract\Entity\EntityInterface;
use App\Entity\User;
use App\Enum\Role;
use App\Tests\Support\FunctionalTester;
use Exception;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures
{
    public const string USER_AUTHORIZED_EMAIL = 'userAuthorized@mail.ru';
    public const string USER_AUTHORIZED_PASSWORD = 'userAuthorizedPassword';

    public static function load(FunctionalTester $I, array $data = []): EntityInterface
    {
        $faker = Factory::create();

        $passwordHasher = $I->grabService(serviceId: UserPasswordHasherInterface::class);

        $roles = $data['roles'] ?? [Role::ROLE_USER->value];
        $email = $data['email'] ?? $faker->email();

        if ($email === self::USER_AUTHORIZED_EMAIL) {
            $password = self::USER_AUTHORIZED_PASSWORD;
        } else {
            $password = $data['password'] ?? $faker->password();
        }

        $password = $passwordHasher->hashPassword(new User(), $password);

        try {
            $entity = $I->grabEntityFromRepository(entity: User::class, params: ['email' => $email]);
        } catch (Exception $e) {
            $id = $I->haveInRepository(classNameOrInstance: User::class, data: [
                'email' => $email,
                'password' => $password,
                'roles' => $roles,
            ]);

            $entity = $I->grabEntityFromRepository(entity: User::class, params: ['id' => $id]);
        }

        return $entity;
    }
}
