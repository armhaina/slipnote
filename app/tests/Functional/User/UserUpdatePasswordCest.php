<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Entity\User;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\Test\TestFailedAuthorizationTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedBadRequestTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedForbiddenTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedValidationTrait;
use App\Tests\Support\Data\Trait\Test\TestSuccessTrait;
use App\Tests\Support\FunctionalTester;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;

final class UserUpdatePasswordCest extends AbstractCest
{
    use TestSuccessTrait;
    use TestFailedBadRequestTrait;
    use TestFailedAuthorizationTrait;
    use TestFailedForbiddenTrait;
    use TestFailedValidationTrait;

    private const string URL = '/api/v1/users';

    protected static function getMethod(): string
    {
        return Request::METHOD_PUT;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        $id = self::getEntity(I: $I, fixtures: $context['fixtures']['major'] ?? [])->getId();

        return self::URL.'/'.$id.'/password';
    }

    protected function successProvider(): array
    {
        return [
            [
                'want_to' => 'Изменить пароль',
                'is_authorize' => true,
                'context' => [
                    'params' => [
                        'current_password' => UserFixture::USER_AUTHORIZED_PASSWORD,
                        'new_password' => 'password123',
                    ],
                    'fixtures' => [
                        'major' => [
                            'email' => UserFixture::USER_AUTHORIZED_EMAIL,
                        ],
                    ],
                ],
                'response' => [
                    'email' => UserFixture::USER_AUTHORIZED_EMAIL,
                ],
            ],
        ];
    }

    protected function failedForbiddenProvider(): array
    {
        return [
            [
                'context' => [
                    'params' => [
                        'current_password' => UserFixture::USER_AUTHORIZED_PASSWORD,
                        'new_password' => 'password123',
                    ],
                    'fixtures' => [
                        'major' => [
                            'email' => 'launch@mail.ru',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function failedValidationProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                'want_to' => 'Пароль мин.',
                'is_authorize' => true,
                'context' => [
                    'params' => [
                        'current_password' => UserFixture::USER_AUTHORIZED_PASSWORD,
                        'new_password' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(5, 5).'}'),
                    ],
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'new_password',
                            'message' => 'Пароль должен содержать минимум 6 символов',
                        ],
                    ],
                ],
            ],
            [
                'want_to' => 'Пароль макс.',
                'is_authorize' => true,
                'context' => [
                    'params' => [
                        'current_password' => UserFixture::USER_AUTHORIZED_PASSWORD,
                        'new_password' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(19, 19).'}'),
                    ],
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'new_password',
                            'message' => 'Пароль должен содержать максимум 18 символов',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function failedBadRequestProvider(): array
    {
        return [
            [
                'want_to' => 'Неверный текущий пароль пользователя',
                'is_authorize' => true,
                'context' => [
                    'params' => [
                        'current_password' => 'invalidate_password',
                        'new_password' => 'password123',
                    ],
                    'fixtures' => [
                        'major' => [
                            'email' => UserFixture::USER_AUTHORIZED_EMAIL,
                        ],
                    ],
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Неверный текущий пароль пользователя',
                ],
            ],
        ];
    }

    protected static function getEntity(FunctionalTester $I, array $fixtures = []): User
    {
        return UserFixture::load(I: $I, data: $fixtures);
    }
}
