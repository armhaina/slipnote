<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Enum\Group;
use App\Enum\Role;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\Entity\User\ForbiddenException;
use App\Mapper\Entity\NoteMapper;
use App\Message\HttpStatusMessage;
use App\Model\Payload\NoteCreatePayloadModel;
use App\Model\Payload\NoteUpdatePayloadModel;
use App\Model\Query\NoteQueryModel;
use App\Model\Response\Action\DeleteResponseModelAction;
use App\Model\Response\Entity\NotePaginationResponseModelEntity;
use App\Model\Response\Entity\NoteResponseModelEntity;
use App\Model\Response\Exception\DefaultResponseModelException;
use App\Model\Response\Exception\ExpiredJWTTokenModelException;
use App\Model\Response\Exception\ForbiddenResponseModelException;
use App\Model\Response\Exception\ValidationResponseModelException;
use App\Service\Entity\NoteService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/notes')]
#[OA\Tag(name: 'notes', description: 'Операции с заметками')]
#[IsGranted(
    attribute: Role::ROLE_USER->value,
    statusCode: Response::HTTP_FORBIDDEN
)]
#[Security(name: 'Bearer')]
class NoteController extends AbstractController
{
    public function __construct(
        private readonly NoteService $noteService,
        private readonly NoteMapper $noteMapper
    ) {}

    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    #[OA\Get(operationId: 'getNote', summary: 'Получить заметку по ID')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: NoteResponseModelEntity::class,
                groups: [Group::PUBLIC->value]
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_FORBIDDEN],
        content: new OA\JsonContent(
            ref: new Model(
                type: ForbiddenResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_INTERNAL_SERVER_ERROR],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNAUTHORIZED],
        content: new OA\JsonContent(
            ref: new Model(
                type: ExpiredJWTTokenModelException::class
            )
        )
    )]
    public function get(Note $note): JsonResponse
    {
        if ($note->getUser() !== $this->getUser()) {
            throw new ForbiddenException();
        }

        $responseModel = $this->noteMapper->one(note: $note);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }

    #[Route(methods: [Request::METHOD_GET])]
    #[OA\Get(
        operationId: 'getListNote',
        summary: 'Получить список заметок',
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: NotePaginationResponseModelEntity::class,
                    groups: [Group::PUBLIC->value]
                )
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNPROCESSABLE_ENTITY],
        content: new OA\JsonContent(
            ref: new Model(
                type: ValidationResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_INTERNAL_SERVER_ERROR],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNAUTHORIZED],
        content: new OA\JsonContent(
            ref: new Model(
                type: ExpiredJWTTokenModelException::class
            )
        )
    )]
    #[OA\Parameter(
        name: 'ids[]',
        description: 'Массив ID заметок',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'array',
            items: new OA\Items(type: 'integer'),
            default: null,
            example: [1, 2, 3]
        ),
        style: 'form',
        explode: true
    )]
    #[OA\Parameter(
        name: 'user_ids[]',
        description: 'Массив ID пользователей (в данный момент работает только с собственным ID)',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'array',
            items: new OA\Items(type: 'integer'),
            default: null,
            example: [1, 2, 3]
        ),
        style: 'form',
        explode: true
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Кол-во записей на странице',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'integer',
            default: 20,
            example: 10
        ),
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Номер страницы',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'integer',
            default: 1,
            example: 10
        ),
    )]
    #[OA\Parameter(
        name: 'updated_at_less',
        description: 'Дата изменения меньше указанной даты',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'string',
            format: 'date-time',
            default: null,
            example: '2030-01-01',
            nullable: true
        ),
    )]
    #[OA\Parameter(
        name: 'order_by[name]',
        description: 'Сортировка по имени',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']),
        example: 'asc',
    )]
    #[OA\Parameter(
        name: 'order_by[created_at]',
        description: 'Сортировка по дате создания',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']),
        example: 'desc',
    )]
    #[OA\Parameter(
        name: 'order_by[updated_at]',
        description: 'Сортировка по дате обновления',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']),
        example: 'desc',
    )]
    public function list(#[MapQueryString] NoteQueryModel $model): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new ForbiddenException();
        }

        $model->setUserIds(userIds: [$user->getId()]);
        $pagination = $this->noteService->pagination(queryModel: $model);

        $responseModel = $this->noteMapper->pagination(pagination: $pagination);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }

    #[Route(methods: [Request::METHOD_POST])]
    #[OA\Post(operationId: 'createNote', summary: 'Создать заметку')]
    #[OA\RequestBody(content: new Model(type: NoteCreatePayloadModel::class))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: NoteResponseModelEntity::class,
                groups: [Group::PUBLIC->value]
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNPROCESSABLE_ENTITY],
        content: new OA\JsonContent(
            ref: new Model(
                type: ValidationResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_INTERNAL_SERVER_ERROR],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNAUTHORIZED],
        content: new OA\JsonContent(
            ref: new Model(
                type: ExpiredJWTTokenModelException::class
            )
        )
    )]
    public function create(#[MapRequestPayload] NoteCreatePayloadModel $model): JsonResponse
    {
        $dateTimeImmutable = new \DateTimeImmutable();

        $note = new Note()
            ->setName(name: $model->getName())
            ->setDescription(description: $model->getDescription())
            ->setUser(user: $this->getUser())
            ->setCreatedAt(dateTimeImmutable: $dateTimeImmutable)
            ->setUpdatedAt(dateTimeImmutable: $dateTimeImmutable)
            ->setIsTrashed(isTrashed: false)
        ;

        $note = $this->noteService->create(entity: $note);

        $responseModel = $this->noteMapper->one(note: $note);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityNotFoundWhenUpdateException
     */
    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_PUT]
    )]
    #[OA\Put(operationId: 'updateNote', summary: 'Изменить заметку по ID')]
    #[OA\RequestBody(content: new Model(type: NoteUpdatePayloadModel::class))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: NoteResponseModelEntity::class,
                groups: [Group::PUBLIC->value]
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNPROCESSABLE_ENTITY],
        content: new OA\JsonContent(
            ref: new Model(
                type: ValidationResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_FORBIDDEN],
        content: new OA\JsonContent(
            ref: new Model(
                type: ForbiddenResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_INTERNAL_SERVER_ERROR],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNAUTHORIZED],
        content: new OA\JsonContent(
            ref: new Model(
                type: ExpiredJWTTokenModelException::class
            )
        )
    )]
    public function update(
        Note $note,
        #[MapRequestPayload]
        NoteUpdatePayloadModel $model,
    ): JsonResponse {
        if ($note->getUser() !== $this->getUser()) {
            throw new ForbiddenException();
        }

        $dateTimeImmutable = new \DateTimeImmutable();

        $note
            ->setName(name: $model->getName())
            ->setDescription(description: $model->getDescription())
            ->setUpdatedAt(dateTimeImmutable: $dateTimeImmutable)
        ;

        $note = $this->noteService->update(entity: $note);

        $responseModel = $this->noteMapper->one(note: $note);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityNotFoundWhenDeleteException
     */
    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_DELETE]
    )]
    #[OA\Delete(operationId: 'deleteNote', summary: 'Удалить заметку по ID')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(type: DeleteResponseModelAction::class)
        )
    )]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_FORBIDDEN],
        content: new OA\JsonContent(
            ref: new Model(
                type: ForbiddenResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_INTERNAL_SERVER_ERROR],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNAUTHORIZED],
        content: new OA\JsonContent(
            ref: new Model(
                type: ExpiredJWTTokenModelException::class
            )
        )
    )]
    public function delete(Note $note): JsonResponse
    {
        if ($note->getUser() !== $this->getUser()) {
            throw new ForbiddenException();
        }

        $this->noteService->delete(entity: $note);

        return $this->json(data: new DeleteResponseModelAction());
    }

    /**
     * @throws EntityNotFoundWhenUpdateException
     */
    #[Route(
        path: '/{id}/trash',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_DELETE]
    )]
    #[OA\Delete(operationId: 'deleteInTrashNote', summary: 'Удалить заметку в корзину по ID')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: NoteResponseModelEntity::class,
                groups: [Group::PUBLIC->value]
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_FORBIDDEN],
        content: new OA\JsonContent(
            ref: new Model(
                type: ForbiddenResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_INTERNAL_SERVER_ERROR],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNAUTHORIZED],
        content: new OA\JsonContent(
            ref: new Model(
                type: ExpiredJWTTokenModelException::class
            )
        )
    )]
    public function deleteInTrash(Note $note): JsonResponse
    {
        if ($note->getUser() !== $this->getUser()) {
            throw new ForbiddenException();
        }

        if (!$note->getIsTrashed()) {
            $note
                ->setIsTrashed(isTrashed: true)
                ->setDeletedAt(deletedAt: new \DateTimeImmutable())
            ;

            $this->noteService->update(entity: $note);
        }

        $responseModel = $this->noteMapper->one(note: $note);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityNotFoundWhenUpdateException
     */
    #[Route(
        path: '/{id}/trash/restore',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_PUT]
    )]
    #[OA\Put(operationId: 'restoreFromTrashNote', summary: 'Восстановить заметку из корзины по ID')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: NoteResponseModelEntity::class,
                groups: [Group::PUBLIC->value]
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_FORBIDDEN],
        content: new OA\JsonContent(
            ref: new Model(
                type: ForbiddenResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_INTERNAL_SERVER_ERROR,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_INTERNAL_SERVER_ERROR],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_UNAUTHORIZED],
        content: new OA\JsonContent(
            ref: new Model(
                type: ExpiredJWTTokenModelException::class
            )
        )
    )]
    public function restoreFromTrash(Note $note): JsonResponse
    {
        if ($note->getUser() !== $this->getUser()) {
            throw new ForbiddenException();
        }

        if ($note->getIsTrashed()) {
            $note
                ->setIsTrashed(isTrashed: false)
                ->setDeletedAt(deletedAt: null)
            ;

            $this->noteService->update(entity: $note);
        }

        $responseModel = $this->noteMapper->one(note: $note);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }
}
