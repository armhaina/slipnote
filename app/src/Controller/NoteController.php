<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Enum\Group;
use App\Enum\Role;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Exception\EntityQueryModel\EntityQueryModelInvalidObjectTypeException;
use App\Mapper\Response\NoteResponseMapper;
use App\Model\Payload\NotePayloadModel;
use App\Model\Query\NoteQueryModel;
use App\Model\Response\NoteResponseModel;
use App\Service\NoteService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/notes')]
#[OA\Tag(name: 'notes')]
#[IsGranted(
    attribute: ROLE::ROLE_USER->value,
    message: 'Вы не авторизованы!',
    statusCode: Response::HTTP_FORBIDDEN
)]
#[Security(name: 'Bearer')]
class NoteController extends AbstractController
{
    public function __construct(
        private readonly NoteService $noteService,
        private readonly NoteResponseMapper $noteResponseMapper
    ) {
    }

    /**
     * @param Note $note
     * @return JsonResponse
     */
    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    #[OA\Get(
        summary: 'Получить заметку по ID',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Успех',
                content: new OA\JsonContent(
                    ref: new Model(
                        type: NoteResponseModel::class,
                        groups: [Group::PUBLIC->value]
                    )
                )
            ),
        ]
    )]
    public function get(Note $note): JsonResponse
    {
        if ($note->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $responseModel = $this->noteResponseMapper->one(note: $note);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    #[Route(methods: [Request::METHOD_GET])]
    #[OA\Get(
        summary: 'Получить список заметок',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Успех',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: NoteResponseModel::class,
                            groups: [Group::PUBLIC->value]
                        )
                    )
                )
            ),
        ]
    )]
    #[OA\Parameter(
        name: 'ids',
        description: 'Массив ID заметок',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'array',
            items: new OA\Items(type: 'integer'),
            default: null,
            example: [1, 2, 3]
        ),
    )]
    #[OA\Parameter(
        name: 'user_ids',
        description: 'Массив ID пользователей (в данный момент работает только с собственным ID)',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'array',
            items: new OA\Items(type: 'integer'),
            default: null,
            example: [1, 2, 3]
        ),
    )]
    #[OA\Parameter(
        name: 'order_by',
        description: 'Сортировка по полям, пример: ?order_by[name]=asc&order_by[created_at]=desc',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'name', type: 'string', enum: ['asc', 'desc']),
            ],
            type: 'object',
            default: ['created_at' => 'desc'],
            additionalProperties: false,
        ),
        style: 'deepObject',
        explode: true,
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
            default: null,
            example: 10
        ),
    )]
    #[OA\Parameter(
        name: 'updated_at_less',
        description: 'Дата изменения меньше указанной даты',
        in: 'query',
        required: false,
        schema: new OA\Schema(
            type: 'integer',
            format: 'date-time',
            default: null,
            example: '2024-01-22T10:30:00+03:00',
            nullable: true
        ),
    )]
    public function list(#[MapQueryString] NoteQueryModel $model): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $model->setUserIds(userIds: [$user->getId()]);
        $notes = $this->noteService->list(queryModel: $model);

        $responseModels = $this->noteResponseMapper->collection(notes: $notes->toArray());

        return $this->json(data: $responseModels, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     */
    #[Route(methods: [Request::METHOD_POST])]
    #[OA\RequestBody(content: new Model(type: NotePayloadModel::class))]
    #[OA\Response(
        response: 201,
        description: 'Заметки созданы',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'total', type: 'integer', example: 2),
            ],
            type: 'object'
        )
    )]
    public function create(#[MapRequestPayload] NotePayloadModel $model): JsonResponse
    {
        $entity = new Note()
            ->setName(name: $model->getName())
            ->setDescription(description: $model->getDescription())
            ->setUser(user: $this->getUser());

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
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_PUT]
    )]
    public function update(
        Note $note,
        #[MapRequestPayload]
        NotePayloadModel $model,
    ): JsonResponse {
        if ($note->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $note
            ->setName(name: $model->getName())
            ->setDescription(description: $model->getDescription());

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
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_DELETE]
    )]
    public function delete(Note $note): JsonResponse
    {
        if ($note->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('User not found or invalid user type');
        }

        $this->noteService->delete(entity: $note);

        return $this->json(data: ['success' => true, 'message' => 'Запись успешно удалена']);
    }
}
