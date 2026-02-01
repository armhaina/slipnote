<?php

namespace App\Tests\Support\Data\Fixture;

use App\Entity\User;
use App\Enum\Entity\User\Role;
use App\Tests\Support\FunctionalTester;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture
{
    public const string USER_AUTHORIZED_EMAIL = 'userAuthorized@mail.ru';
    public const string USER_AUTHORIZED_PASSWORD = 'userAuthorizedPassword';

    public static function load(FunctionalTester $I, array $data = []): User
    {
        $faker = Factory::create();

        $dateTimeImmutable = new \DateTimeImmutable();

        $passwordHasher = $I->grabService(serviceId: UserPasswordHasherInterface::class);

        $roles = $data['roles'] ?? [Role::ROLE_USER->value];
        $email = $data['email'] ?? $faker->email();
        $createdAt = $data['created_at'] ?? $dateTimeImmutable;
        $updatedAt = $data['updated_at'] ?? $dateTimeImmutable;

        if (self::USER_AUTHORIZED_EMAIL === $email) {
            $password = self::USER_AUTHORIZED_PASSWORD;
        } else {
            $password = $data['password'] ?? $faker->password();
        }

        $password = $passwordHasher->hashPassword(new User(), $password);

        try {
            $entity = $I->grabEntityFromRepository(entity: User::class, params: ['email' => $email]);
        } catch (\Exception $e) {
            $id = $I->haveInRepository(classNameOrInstance: User::class, data: [
                'email' => $email,
                'password' => $password,
                'roles' => $roles,
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
            ]);

            $entity = $I->grabEntityFromRepository(entity: User::class, params: ['id' => $id]);
        }

        return $entity;
    }
}
