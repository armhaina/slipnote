<?php

declare(strict_types=1);

namespace App\Mapper\Response;

use App\Contract\EntityInterface;
use App\Entity\Note;
use App\Entity\User;
use App\Enum\Group;
use App\Model\Response\NoteResponseModel;
use App\Model\Response\UserResponseModel;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

readonly class NoteResponseMapper
{
    /**
     * @param array<string, mixed> $context
     */
    public function one(Note $note, array $context = []): NoteResponseModel
    {
        $user = $note->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('Note user must be instance of App\Entity\User');
        }

        return new NoteResponseModel(
            id: $note->getId(),
            name: $note->getName(),
            description: $note->getDescription(),
            user: $this->user(user: $user, context: $context),
        );
    }

    /**
     * @param array<string, mixed> $context
     * @param EntityInterface[] $notes
     * @return NoteResponseModel[]
     */
    public function collection(array $notes, array $context = []): array
    {
        /** @var Note[] $notes */
        return array_map(
            fn(Note $note) => $this->one(note: $note, context: $context),
            $notes
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    private function user(User $user, array $context = []): UserResponseModel
    {
        return new UserResponseModel(
            id: $user->getId(),
            email: $user->getEmail(),
        );
    }
}
