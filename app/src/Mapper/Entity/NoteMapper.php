<?php

declare(strict_types=1);

namespace App\Mapper\Entity;

use App\Entity\Note;
use App\Entity\User;
use App\Model\Response\Entity\NoteResponseModelEntity;
use App\Model\Response\Entity\UserResponseModelEntity;

readonly class NoteMapper
{
    /**
     * @param array<string, mixed> $context
     */
    public function one(Note $note, array $context = []): NoteResponseModelEntity
    {
        $user = $note->getUser();

        // TODO: переделать
        if (!$user instanceof User) {
            throw new \RuntimeException('Note user must be instance of App\Entity\User');
        }

        return new NoteResponseModelEntity(
            id: $note->getId(),
            name: $note->getName(),
            description: $note->getDescription(),
            user: $this->user(user: $user, context: $context),
        );
    }

    /**
     * @param array<string, mixed> $context
     * @param Note[]               $notes
     *
     * @return NoteResponseModelEntity[]
     */
    public function collection(array $notes, array $context = []): array
    {
        return array_map(
            fn (Note $note): NoteResponseModelEntity => $this->one(note: $note, context: $context),
            $notes
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    private function user(User $user, array $context = []): UserResponseModelEntity
    {
        return new UserResponseModelEntity(
            id: $user->getId(),
            email: $user->getEmail(),
        );
    }
}
