<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Enum\Group;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Exception\EntityQueryModel\EntityQueryModelInvalidObjectTypeException;
use App\Model\Payload\NotePayloadModel;
use App\Model\Query\NoteQueryModel;
use App\Service\NoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1/notes')]
class NoteController extends AbstractController
{
    public function __construct(
        private readonly NoteService $noteService,
    ) {}

    /**
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    #[Route(
        path: '/{note}',
        requirements: ['note' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    public function get(Note $note, #[CurrentUser] User $user): JsonResponse
    {
        if ($note->getIsPrivate() && $note->getUser() !== $user) {
            throw new \Exception();
        }

        return $this->json(data: $note, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    #[Route(
        methods: [Request::METHOD_GET]
    )]
    public function list(#[MapQueryString] NoteQueryModel $model): JsonResponse
    {
        if (empty($model->getUserIds())
            || in_array(needle: $this->getUser()->getId(), haystack: $model->getUserIds())
        ) {
            $model->setOwnUserId(ownUserId: $this->getUser()->getId());
        }

        $list = $this->noteService->list(queryModel: $model);

        return $this->json(data: $list, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     */
    #[Route(methods: [Request::METHOD_POST])]
    public function create(#[MapRequestPayload] NotePayloadModel $model): JsonResponse
    {
        $entity = (new Note())
            ->setName(name: $model->getName())
            ->setDescription(description: $model->getDescription())
            ->setIsPrivate(isPrivate: $model->getIsPrivate())
            ->setUser(user: $this->getUser())
        ;

        $entity = $this->noteService->create(entity: $entity);

        return $this->json(data: $entity, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityNotFoundWhenUpdateException
     * @throws EntityInvalidObjectTypeException
     * @throws \Exception
     */
    #[Route(
        path: '/{note}',
        requirements: ['note' => '\d+'],
        methods: [Request::METHOD_PUT]
    )]
    public function update(
        Note $note,
        #[MapRequestPayload]
        NotePayloadModel $model,
    ): JsonResponse {
        if ($note->getUser() !== $this->getUser()) {
            throw new \Exception();
        }

        $note
            ->setName(name: $model->getName())
            ->setDescription(description: $model->getDescription())
            ->setIsPrivate(isPrivate: $model->getIsPrivate())
        ;

        $entity = $this->noteService->update(entity: $note);

        return $this->json(data: $entity, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityNotFoundWhenDeleteException
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityInvalidObjectTypeException
     * @throws \Exception
     */
    #[Route(
        path: '/{note}',
        requirements: ['note' => '\d+'],
        methods: [Request::METHOD_DELETE]
    )]
    public function delete(Note $note): JsonResponse
    {
        if ($note->getUser() !== $this->getUser()) {
            throw new \Exception();
        }

        $this->noteService->delete(entity: $note);

        return $this->json(data: ['success' => true, 'message' => 'Запись успешно удалена']);
    }
}
