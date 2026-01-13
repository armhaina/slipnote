<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\Group;
use App\Enum\Role;
use App\Exception\Entity\EntityInvalidObjectTypeException;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Exception\Entity\EntityNotFoundWhenUpdateException;
use App\Exception\EntityModel\EntityModelInvalidObjectTypeException;
use App\Model\Payload\UserPayloadModel;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route(
        path: '/{user}',
        requirements: ['user' => '\d+'],
        methods: [Request::METHOD_GET]
    )]
    public function get(User $user): JsonResponse
    {
        return $this->json(data: $user, context: ['groups' => [Group::PUBLIC->value]]);
    }

    /**
     * @throws EntityInvalidObjectTypeException
     * @throws EntityModelInvalidObjectTypeException
     */
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
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityNotFoundWhenUpdateException
     * @throws EntityInvalidObjectTypeException
     * @throws \Exception
     */
    #[Route(
        path: '/{user}',
        requirements: ['user' => '\d+'],
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
     * @throws EntityModelInvalidObjectTypeException
     * @throws EntityNotFoundWhenDeleteException
     * @throws EntityInvalidObjectTypeException
     * @throws \Exception
     */
    #[Route(
        path: '/{user}',
        requirements: ['user' => '\d+'],
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
