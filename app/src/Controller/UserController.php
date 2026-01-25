<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\Group;
use App\Enum\Role;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Model\Payload\UserPayloadModel;
use App\Service\UserService;
use Nelmio\ApiDocBundle\Attribute\Security;
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
#[OA\Tag(name: 'users')]
#[IsGranted(
    attribute: Role::ROLE_USER->value,
    message: 'Вы не авторизованы!',
    statusCode: Response::HTTP_FORBIDDEN
)]
#[Security(name: 'Bearer')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    public function get(User $user): JsonResponse
    {
        return $this->json(data: $user, context: ['groups' => [Group::PUBLIC->value]]);
    }

    #[Route(methods: [Request::METHOD_POST])]
    public function create(#[MapRequestPayload] UserPayloadModel $model): JsonResponse
    {
        $entity = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($entity, $model->getPassword());

        $entity
            ->setEmail(email: $model->getEmail())
            ->setPassword(password: $hashedPassword)
            ->setRoles(roles: [Role::ROLE_USER->value])
        ;

        $entity = $this->userService->create(entity: $entity);

        return $this->json(data: $entity, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityNotFoundWhenUpdateException
     */
    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_PUT]
    )]
    public function update(
        User $user,
        #[MapRequestPayload]
        UserPayloadModel $model
    ): JsonResponse {
        if ($user !== $this->getUser()) {
            throw new \Exception();
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $model->getPassword());

        $user
            ->setEmail(email: $model->getEmail())
            ->setPassword(password: $hashedPassword)
        ;

        $this->userService->update(entity: $user);

        return $this->json(data: $user, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityNotFoundWhenDeleteException
     */
    #[Route(
        path: '/{id}',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_DELETE]
    )]
    public function delete(User $user): JsonResponse
    {
        if ($user !== $this->getUser()) {
            throw new \Exception();
        }

        $this->userService->delete(entity: $user);

        return $this->json(data: ['success' => true, 'message' => 'Запись успешно удалена']);
    }
}
