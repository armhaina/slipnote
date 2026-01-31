<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\Test\TestFailedConflictTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedValidationTrait;
use App\Tests\Support\Data\Trait\Test\TestSuccessTrait;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;

final class UserCreateCest extends AbstractCest
{
    use TestSuccessTrait;
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
        if (!empty($context['fixtures'])) {
            UserFixture::load(I: $I, data: $context['fixtures']);
        }
    }

    //    #[DataProvider('failedEmailAlreadyExistsProvider')]
    //    public function failedEmailAlreadyExists(FunctionalTester $I, Example $example): void
    //    {
    //        $I->wantTo('POST/409: Почта уже существует');
    //
    //        UserFixture::load(I: $I, data: $example['fixtures']);
    //
    //        $I->sendPost(url: self::URL, params: $example['request']);
    //        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
    //        $I->seeResponseIsJson();
    //
    //        $data = json_decode($I->grabResponse(), true);
    //        $data = self::except(data: $data, excludeKeys: ['id']);
    //
    //        $I->assertEquals(expected: $example['response'], actual: $data);
    //    }

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
                    'message' => 'Ошибка валидации',
                    'errors' => [
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
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'password',
                            'message' => 'Пароль должен содержать минимум 6 символов',
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
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'password',
                            'message' => 'Пароль должен содержать максимум 18 символов',
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
                        'email' => 'create@mail.ru',
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
