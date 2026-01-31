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
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Symfony\Component\HttpFoundation\Request;

final class UserUpdateCest extends AbstractCest
{
    use TestSuccessTrait;
    use TestFailedAuthorizationTrait;
    use TestFailedForbiddenTrait;

    private const string URL = '/api/v1/users';

    protected static function getMethod(): string
    {
        return Request::METHOD_PUT;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        $id = self::getEntity(I: $I, fixtures: $context['fixtures'] ?? [])->getId();

        return self::URL.'/'.$id;
    }

    //    #[DataProvider('mainProvider')]
    //    public function main(FunctionalTester $I, Example $example): void
    //    {
    //        $I->wantTo('PUT/200: Изменить пользователя');
    //
    //        $user = $this->authorized(I: $I);
    //
    //        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
    //        $I->seeResponseCodeIs(code: HttpCode::OK);
    //        $I->seeResponseIsJson();
    //
    //        $data = json_decode($I->grabResponse(), true);
    //        $data = self::except(data: $data, excludeKeys: ['id']);
    //
    //        $I->assertEquals(expected: $example['response'], actual: $data);
    //    }
    //
    //    #[DataProvider('failedAuthorizationProvider')]
    //    public function failedAuthorization(FunctionalTester $I, Example $example): void
    //    {
    //        $I->wantTo('PUT/401: Ошибка авторизации');
    //        $user = UserFixture::load(I: $I);
    //
    //        $I->sendPut(url: self::URL.'/'.$user->getId());
    //        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
    //        $I->seeResponseIsJson();
    //
    //        $data = json_decode($I->grabResponse(), true);
    //        $data = self::except(data: $data, excludeKeys: ['id']);
    //
    //        $I->assertEquals(expected: $example['response'], actual: $data);
    //    }
    //
    //    #[DataProvider('forbiddenProvider')]
    //    public function forbidden(FunctionalTester $I, Example $example): void
    //    {
    //        $I->wantTo('PUT/403: Доступ запрещен');
    //
    //        $this->authorized(I: $I);
    //        $user = UserFixture::load(I: $I);
    //
    //        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
    //        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
    //        $I->seeResponseIsJson();
    //
    //        $data = json_decode($I->grabResponse(), true);
    //        $data = self::except(data: $data, excludeKeys: ['id']);
    //
    //        $I->assertEquals(expected: $example['response'], actual: $data);
    //    }
    //
    //    #[DataProvider('failedEmailAlreadyExistsProvider')]
    //    public function failedEmailAlreadyExists(FunctionalTester $I, Example $example): void
    //    {
    //        $I->wantTo('PUT/409: Почта уже существует');
    //
    //        $user = $this->authorized(I: $I);
    //        UserFixture::load(I: $I, data: $example['fixtures']);
    //
    //        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
    //        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
    //        $I->seeResponseIsJson();
    //
    //        $data = json_decode($I->grabResponse(), true);
    //        $data = self::except(data: $data, excludeKeys: ['id']);
    //
    //        $I->assertEquals(expected: $example['response'], actual: $data);
    //    }
    //
    //    #[DataProvider('failedValidationProvider')]
    //    public function failedValidation(FunctionalTester $I, Example $example): void
    //    {
    //        $I->wantTo('PUT/422: Ошибка валидации');
    //
    //        $user = $this->authorized(I: $I);
    //
    //        $I->sendPut(url: self::URL.'/'.$user->getId(), params: $example['request']);
    //        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
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
                'want_to' => 'Изменить пользователя (только текущий пользователь)',
                'is_authorize' => true,
                'context' => [
                    'fixtures' => [
                        'email' => UserFixture::USER_AUTHORIZED_EMAIL,
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
                        'email' => 'launch@mail.ru',
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
                'request' => [
                    'email' => 'test',
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

    protected function failedEmailAlreadyExistsProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'email' => 'update@mail.ru',
                ],
                'request' => [
                    'email' => 'update@mail.ru',
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Пользователь с почтой update@mail.ru уже существует',
                ],
            ],
        ];
    }

    private static function getEntity(FunctionalTester $I, array $fixtures = []): User
    {
        return UserFixture::load(I: $I, data: $fixtures);
    }
}
