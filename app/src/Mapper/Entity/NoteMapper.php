<?php

declare(strict_types=1);

namespace App\Mapper\Entity;

use App\Entity\Note;
use App\Entity\User;
use App\Model\Response\Entity\NotePaginationResponseModelEntity;
use App\Model\Response\Entity\NoteResponseModelEntity;
use App\Model\Response\Entity\UserResponseModelEntity;
use App\Service\PaginationService;
use Knp\Component\Pager\Pagination\PaginationInterface;

readonly class NoteMapper
{
    /**
     * @param array<string, mixed> $context
     */
    public function one(Note $note, array $context = []): NoteResponseModelEntity
    {
        $user = $note->getUser();

        return new NoteResponseModelEntity(
            id: $note->getId(),
            name: $note->getName(),
            description: $note->getDescription(),
            isTrashed: $note->getIsTrashed(),
            user: $this->user(user: $user, context: $context)
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
     * @param array<string, mixed>           $context
     * @param PaginationInterface<int, Note> $pagination
     */
    public function pagination(PaginationInterface $pagination, array $context = []): NotePaginationResponseModelEntity
    {
        return new NotePaginationResponseModelEntity(
            count: $pagination->count(),
            page: $pagination->getCurrentPageNumber(),
            total: $pagination->getTotalItemCount(),
            pages: PaginationService::getPages(pagination: $pagination),
            items: $this->collection(notes: $pagination->getItems())
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
