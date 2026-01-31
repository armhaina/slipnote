<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Entity\User;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\Test\TestFailedAuthorizationTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedConflictTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedForbiddenTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedValidationTrait;
use App\Tests\Support\Data\Trait\Test\TestSuccessTrait;
use App\Tests\Support\FunctionalTester;
use Symfony\Component\HttpFoundation\Request;

final class UserUpdateCest extends AbstractCest
{
    use TestSuccessTrait;
    use TestFailedAuthorizationTrait;
    use TestFailedForbiddenTrait;
    use TestFailedConflictTrait;
    use TestFailedValidationTrait;

    private const string URL = '/api/v1/users';

    protected static function getMethod(): string
    {
        return Request::METHOD_PUT;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        $id = self::getEntity(I: $I, fixtures: $context['fixtures']['major'] ?? [])->getId();

        return self::URL.'/'.$id;
    }

    protected static function getEntity(FunctionalTester $I, array $fixtures = []): User
    {
        return UserFixture::load(I: $I, data: $fixtures);
    }

    protected static function contextHandle(FunctionalTester $I, array &$context): void
    {
        if (!empty($context['fixtures']['minor'])) {
            foreach ($context['fixtures']['minor'] as $fixture) {
                UserFixture::load(I: $I, data: $fixture);
            }
        }
    }

    protected function successProvider(): array
    {
        return [
            [
                'want_to' => 'Изменить пользователя (только текущий пользователь)',
                'is_authorize' => true,
                'context' => [
                    'fixtures' => [
                        'major' => [
                            'email' => UserFixture::USER_AUTHORIZED_EMAIL,
                        ],
                    ],
                    'params' => [
                        'email' => 'update@mail.ru',
                    ],
                ],
                'response' => [
                    'email' => 'update@mail.ru',
                ],
            ],
        ];
    }

    protected function failedForbiddenProvider(): array
    {
        return [
            [
                'context' => [
                    'fixtures' => [
                        'major' => [
                            'email' => 'launch@mail.ru',
                        ],
                    ],
                    'params' => [
                        'email' => 'update@mail.ru',
                    ],
                ],
            ],
        ];
    }

    protected function failedValidationProvider(): array
    {
        return [
            [
                'want_to' => 'Неверный формат Email',
                'context' => [
                    'params' => [
                        'email' => 'test',
                        'password' => 'Password123',
                    ],
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'email',
                            'message' => 'Email не соответствует формату электронной почты',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function failedConflictProvider(): array
    {
        return [
            [
                'want_to' => 'Дублирование Email',
                'is_authorize' => true,
                'context' => [
                    'params' => [
                        'email' => 'create@mail.ru',
                    ],
                    'fixtures' => [
                        'minor' => [
                            [
                                'email' => 'create@mail.ru',
                            ],
                        ],
                        'major' => [
                            'email' => UserFixture::USER_AUTHORIZED_EMAIL,
                        ],
                    ],
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Пользователь с почтой create@mail.ru уже существует',
                ],
            ],
        ];
    }
}
