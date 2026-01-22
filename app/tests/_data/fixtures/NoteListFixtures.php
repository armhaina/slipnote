<?php

namespace App\Tests\_data\fixtures;

use App\Entity\Note;
use App\Entity\User;
use App\Enum\Role;
use App\Tests\Support\FunctionalTester;
use Codeception\Example;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class NoteListFixtures
{
    public function load(FunctionalTester $I, Example $example): void
    {
        for ($i = 0; $i < 2; ++$i) {
            $user = (new User())
                ->setEmail(email: 'test_' . $i . '@mail.ru')
                ->setPassword(password: md5((string)rand()))
                ->setRoles(roles: [Role::ROLE_USER->value]);

            $note = new Note();
            $note->setName(name: 'Заметка_' . $i);
            $note->setDescription(description: 'Описание заметки_' . $i);
            $note->setIsPrivate(isPrivate: false);
            $note->setUser(user: $user);

            $manager->persist($user);
            $manager->persist($note);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return self::GROUPS;
    }
}
