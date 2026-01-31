<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Entity\User;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\Test\TestFailedAuthorizationTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedForbiddenTrait;
use App\Tests\Support\Data\Trait\Test\TestSuccessTrait;
use App\Tests\Support\FunctionalTester;
use Symfony\Component\HttpFoundation\Request;

final class UserDeleteCest extends AbstractCest
{
    use TestSuccessTrait;
    use TestFailedAuthorizationTrait;
    use TestFailedForbiddenTrait;

    private const string URL = '/api/v1/users';

    protected static function getMethod(): string
    {
        return Request::METHOD_DELETE;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        $id = self::getEntity(I: $I, fixtures: $context['fixtures'] ?? [])->getId();

        return self::URL.'/'.$id;
    }

    protected function successProvider(): array
    {
        return [
            [
                'want_to' => 'Удалить пользователя',
                'is_authorize' => true,
                'context' => [
                    'fixtures' => [
                        'email' => UserFixture::USER_AUTHORIZED_EMAIL,
                    ],
                ],
                'response' => [
                    'success' => true,
                    'message' => 'Запись успешно удалена',
                ],
            ],
        ];
    }

    private static function getEntity(FunctionalTester $I, array $fixtures = []): User
    {
        return UserFixture::load(I: $I, data: $fixtures);
    }
}
