<?php

namespace App\Tests\_data\fixtures;

use App\Entity\Note;
use App\Tests\Support\FunctionalTester;
use Faker\Factory;

class NoteFixtures
{
    public static function load(FunctionalTester $I, array $data = []): Note
    {
        $faker = Factory::create();

        $dateTimeImmutable = new \DateTimeImmutable();

        $name = $data['name'] ?? $faker->name;
        $description = $data['description'] ?? $faker->text();
        $isTrashed = $data['is_trashed'] ?? false;
        $user = UserFixtures::load(I: $I, data: $data['user'] ?? []);
        $deletedAt = $data['deleted_at'] ?? null;
        $createdAt = $data['created_at'] ?? $dateTimeImmutable;
        $updatedAt = $data['updated_at'] ?? $dateTimeImmutable;

        $id = $I->haveInRepository(classNameOrInstance: Note::class, data: [
            'name' => $name,
            'description' => $description,
            'isTrashed' => $isTrashed,
            'user' => $user,
            'deletedAt' => $deletedAt,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ]);

        return $I->grabEntityFromRepository(entity: Note::class, params: ['id' => $id]);
    }
}
