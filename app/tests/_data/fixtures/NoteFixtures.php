<?php

namespace App\Tests\_data\fixtures;

use App\Contract\Entity\EntityInterface;
use App\Entity\Note;
use App\Tests\Support\FunctionalTester;
use Faker\Factory;

class NoteFixtures
{
    public static function load(FunctionalTester $I, array $data = []): Note
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
