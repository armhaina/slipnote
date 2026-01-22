<?php

namespace App\Tests\_data\fixtures;

use App\Contract\EntityInterface;
use App\Entity\Note;
use App\Entity\User;
use App\Enum\Role;
use App\Tests\Support\FunctionalTester;
use Codeception\Example;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NoteFixtures
{
    public static function load(FunctionalTester $I, array $data = []): EntityInterface
    {
        $faker = Factory::create();

        $name = $data['name'] ?? $faker->name;
        $description = $data['description'] ?? $faker->text();
        $user = UserFixtures::load(I: $I, data: $data['user'] ?? []);

        $id = $I->haveInRepository(classNameOrInstance: Note::class, data: [
            'name' => $name,
            'description' => $description,
            'user' => $user,
        ]);

        return $I->grabEntityFromRepository(entity: Note::class, params: ['id' => $id]);
    }
}
