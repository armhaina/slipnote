<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\Group;
use App\Enum\Role;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\Entity\User\ForbiddenException;
use App\Exception\Entity\User\UserWithThisEmailAlreadyExistsException;
use App\Mapper\Entity\UserMapper;
use App\Message\HttpStatusMessage;
use App\Model\Payload\UserPayloadModel;
use App\Model\Response\Action\DeleteResponseModelAction;
use App\Model\Response\Entity\UserResponseModelEntity;
use App\Model\Response\Exception\DefaultResponseModelException;
use App\Model\Response\Exception\ForbiddenResponseModelException;
use App\Model\Response\Exception\ValidationResponseModelException;
use App\Service\Entity\UserService;
use App\Service\SecurityService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security as DocSecurity;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/users')]
#[OA\Tag(name: 'users', description: 'Операции с пользователями')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserMapper $userMapper,
        private readonly SecurityService $securityService
    ) {}

    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    #[IsGranted(attribute: Role::ROLE_USER->value, statusCode: Response::HTTP_FORBIDDEN)]
    #[DocSecurity(name: 'Bearer')]
    #[OA\Get(operationId: 'getUser', summary: 'Получить пользователя (только текущий пользователь)')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: UserResponseModelEntity::class,
                groups: [Group::PUBLIC->value]
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
        response: Response::HTTP_FORBIDDEN,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_FORBIDDEN],
        content: new OA\JsonContent(
            ref: new Model(
                type: ForbiddenResponseModelException::class
            )
        )
    )]
    public function get(User $user): JsonResponse
    {
        if ($user !== $this->getUser()) {
            throw new ForbiddenException();
        }

        $responseModel = $this->userMapper->one(user: $user);

        return $this->json(data: $responseModel, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws UserWithThisEmailAlreadyExistsException
     */
    #[Route(methods: [Request::METHOD_POST])]
    #[DocSecurity]
    #[OA\Post(operationId: 'createUser', summary: 'Создать пользователя')]
    #[OA\RequestBody(content: new Model(type: UserPayloadModel::class))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: UserResponseModelEntity::class,
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
        response: Response::HTTP_CONFLICT,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_CONFLICT],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
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
    public function create(
        #[MapRequestPayload]
        UserPayloadModel $model,
        Request $request
    ): JsonResponse {
        $token = $this->securityService->getBearerToken(request: $request);

        if ($token && $this->securityService->isValidJwtToken(token: $token)) {
            throw new ForbiddenException();
        }

        if ($this->userService->checkExistsEmail(email: $model->getEmail())) {
            throw new UserWithThisEmailAlreadyExistsException(email: $model->getEmail());
        }

        $dateTimeImmutable = new \DateTimeImmutable();

        $entity = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($entity, $model->getPassword());

        $entity
            ->setEmail(email: $model->getEmail())
            ->setPassword(password: $hashedPassword)
            ->setRoles(roles: [Role::ROLE_USER->value])
            ->setCreatedAt(dateTimeImmutable: $dateTimeImmutable)
            ->setUpdatedAt(dateTimeImmutable: $dateTimeImmutable)
        ;

        $entity = $this->userService->create(entity: $entity);

        $responseModel = $this->userMapper->one(user: $entity);

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
    #[IsGranted(attribute: Role::ROLE_USER->value, statusCode: Response::HTTP_FORBIDDEN)]
    #[DocSecurity(name: 'Bearer')]
    #[OA\Put(operationId: 'updateUser', summary: 'Изменить пользователя (только текущий пользователь)')]
    #[OA\RequestBody(content: new Model(type: UserPayloadModel::class))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_OK],
        content: new OA\JsonContent(
            ref: new Model(
                type: UserResponseModelEntity::class,
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
        response: Response::HTTP_CONFLICT,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_CONFLICT],
        content: new OA\JsonContent(
            ref: new Model(
                type: DefaultResponseModelException::class
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
        response: Response::HTTP_FORBIDDEN,
        description: HttpStatusMessage::HTTP_STATUS_MESSAGE[Response::HTTP_FORBIDDEN],
        content: new OA\JsonContent(
            ref: new Model(
                type: ForbiddenResponseModelException::class
            )
        )
    )]
    public function update(
        User $user,
        #[MapRequestPayload]
        UserPayloadModel $model
    ): JsonResponse {
        if ($user !== $this->getUser()) {
            throw new ForbiddenException();
        }

        if ($this->userService->checkExistsEmail(email: $model->getEmail())) {
            throw new UserWithThisEmailAlreadyExistsException(email: $model->getEmail());
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $model->getPassword());

        $user
            ->setEmail(email: $model->getEmail())
            ->setPassword(password: $hashedPassword)
            ->setUpdatedAt(dateTimeImmutable: new \DateTimeImmutable())
        ;

        $entity = $this->userService->update(entity: $user);

        $responseModel = $this->userMapper->one(user: $entity);

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
    #[IsGranted(attribute: Role::ROLE_USER->value, statusCode: Response::HTTP_FORBIDDEN)]
    #[DocSecurity(name: 'Bearer')]
    #[OA\Delete(operationId: 'deleteUser', summary: 'Удалить пользователя по ID (только текущий пользователь)')]
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
    public function delete(User $user): JsonResponse
    {
        if ($user !== $this->getUser()) {
            throw new ForbiddenException();
        }

        $this->userService->delete(entity: $user);

        return $this->json(data: new DeleteResponseModelAction());
    }
}
