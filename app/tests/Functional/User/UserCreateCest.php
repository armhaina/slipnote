<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\Test\TestFailedConflictTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedForbiddenTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedValidationTrait;
use App\Tests\Support\Data\Trait\Test\TestSuccessTrait;
use App\Tests\Support\FunctionalTester;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;

final class UserCreateCest extends AbstractCest
{
    use TestSuccessTrait;
    use TestFailedForbiddenTrait;
    use TestFailedConflictTrait;
    use TestFailedValidationTrait;

    private const string URL = '/api/v1/users';

    protected static function getMethod(): string
    {
        return Request::METHOD_POST;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        return self::URL;
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
                'want_to' => 'Создать пользователя',
                'context' => [
                    'params' => [
                        'email' => 'create@mail.ru',
                        'password' => 'createPassword',
                    ],
                ],
                'response' => [
                    'email' => 'create@mail.ru',
                ],
            ],
        ];
    }

    protected function failedForbiddenProvider(): array
    {
        return [
            [
                'want_to' => 'Создать пользователя',
                'context' => [
                    'params' => [
                        'email' => 'create@mail.ru',
                        'password' => 'createPassword',
                    ],
                ],
                'response' => [
                    'email' => 'create@mail.ru',
                ],
            ],
        ];
    }

    protected function failedValidationProvider(): array
    {
        $faker = Factory::create();

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
                    'code' => 0,
                    'message' => 'Ошибка валидации',
                    'violations' => [
                        [
                            'property' => 'email',
                            'message' => 'Email не соответствует формату электронной почты',
                        ],
                    ],
                ],
            ],
            [
                'want_to' => 'Пароль мин.',
                'context' => [
                    'params' => [
                        'email' => 'test@mail.ru',
                        'password' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(5, 5).'}'),
                    ],
                ],
                'response' => [
                    'success' => false,
                    'code' => 0,
                    'message' => 'Ошибка валидации',
                    'violations' => [
                        [
                            'property' => 'password',
                            'message' => 'Минимально допустимое кол-во символов: 6. Ваше кол-во символов: 5',
                        ],
                    ],
                ],
            ],
            [
                'want_to' => 'Пароль макс.',
                'context' => [
                    'params' => [
                        'email' => 'test@mail.ru',
                        'password' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(19, 19).'}'),
                    ],
                ],
                'response' => [
                    'success' => false,
                    'code' => 0,
                    'message' => 'Ошибка валидации',
                    'violations' => [
                        [
                            'property' => 'password',
                            'message' => 'Максимально допустимое кол-во символов: 18. Ваше кол-во символов: 19',
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
                'context' => [
                    'params' => [
                        'email' => 'create@mail.ru',
                        'password' => 'createPassword',
                    ],
                    'fixtures' => [
                        'minor' => [
                            [
                                'email' => 'create@mail.ru',
                            ],
                        ],
                    ],
                ],
                'response' => [
                    'success' => false,
                    'code' => 0,
                    'message' => 'Пользователь с почтой create@mail.ru уже существует',
                    'violations' => [],
                ],
            ],
        ];
    }
}
