<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAuthorizedFixtures extends Fixture implements FixtureGroupInterface
{
    public const EMAIL = 'userAuthorized@mail.ru';
    public const PASSWORD = 'userAuthorizedPassword';
    public const GROUPS = ['user-authorized'];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $entity = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($entity, self::PASSWORD);

        $entity
            ->setEmail(email: self::EMAIL)
            ->setPassword(password: $hashedPassword)
            ->setRoles(roles: [Role::ROLE_USER->value])
        ;

        $manager->persist($entity);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return self::GROUPS;
    }
}
