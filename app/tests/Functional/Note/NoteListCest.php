<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\Tests\_data\fixtures\NoteFixtures;
use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

final class NoteListCest extends AbstractCest
{
    private const string URL = '/api/v1/notes';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture);
        }

        $I->sendGet(url: self::URL);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('idsProvider')]
    public function paramIds(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [ids]');

        $this->authorized(I: $I);

        $noteIds = [];

        foreach ($example['fixtures'] as $fixture) {
            $noteIds[] = NoteFixtures::load(I: $I, data: $fixture)->getId();
        }

        $I->sendGet(url: self::URL, params: ['ids' => $noteIds]);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('userIdsProvider')]
    public function paramUserIds(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [user_ids]');

        $this->authorized(I: $I);

        $users = [];

        foreach ($example['fixtures'] as $fixture) {
            $note = NoteFixtures::load(I: $I, data: $fixture);
            $users[] = $note->getUser()->getId();
        }

        $I->sendGet(url: self::URL, params: ['user_ids' => $users]);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('updatedAtLessProvider')]
    public function paramUpdatedAtLess(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [updated_at_less]');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture);
        }

        $I->sendGet(url: self::URL, params: ['updated_at_less' => $example['query']['updated_at_less']]);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('orderByNameAscProvider')]
    public function paramOrderByNameAsc(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [order_by[name]=asc]');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture)->getId();
        }

        $I->sendGet(url: self::URL, params: ['order_by[name]' => 'asc']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('orderByNameDescProvider')]
    public function paramOrderByNameDesc(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [order_by[name]=desc]');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture)->getId();
        }

        $I->sendGet(url: self::URL, params: ['order_by[name]' => 'desc']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('orderByCreatedAtAscProvider')]
    public function paramOrderByCreatedAtAsc(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [order_by[created_at]=asc]');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture)->getId();
        }

        $I->sendGet(url: self::URL, params: ['order_by[created_at]' => 'asc']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('orderByCreatedAtDescProvider')]
    public function paramOrderByCreatedAtDesc(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [order_by[created_at]=desc]');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture)->getId();
        }

        $I->sendGet(url: self::URL, params: ['order_by[created_at]' => 'desc']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('orderByUpdatedAtAscProvider')]
    public function paramOrderByUpdatedAtAsc(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [order_by[updated_at]=asc]');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture)->getId();
        }

        $I->sendGet(url: self::URL, params: ['order_by[updated_at]' => 'asc']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('orderByUpdatedAtDescProvider')]
    public function paramOrderByUpdatedAtDesc(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить список заметок с параметром [order_by[updated_at]=desc]');

        $this->authorized(I: $I);

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture)->getId();
        }

        $I->sendGet(url: self::URL, params: ['order_by[updated_at]' => 'desc']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('failedAuthorizationProvider')]
    public function failedAuthorization(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/401: Ошибка авторизации');

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture);
        }

        $I->sendGet(url: self::URL);
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function mainProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => 'test_0@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'count' => 1,
                    'page' => 1,
                    'total' => 1,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function failedAuthorizationProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => 'test_0@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'code' => 401,
                    'message' => 'JWT Token not found',
                ],
            ],
        ];
    }

    protected function userIdsProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => 'test_0@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'count' => 1,
                    'page' => 1,
                    'total' => 1,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function updatedAtLessProvider(): array
    {
        return [
            [
                'query' => [
                    'updated_at_less' => new \DateTimeImmutable('01.02.2025')->format(format: DATE_ATOM),
                ],
                'fixtures' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        'updated_at' => new \DateTimeImmutable('01.03.2025'),
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        'updated_at' => new \DateTimeImmutable('01.01.2025'),
                    ],
                ],
                'response' => [
                    'count' => 1,
                    'page' => 1,
                    'total' => 1,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function idsProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => 'test_0@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'count' => 1,
                    'page' => 1,
                    'total' => 1,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function orderByNameAscProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'count' => 2,
                    'page' => 1,
                    'total' => 2,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_0',
                            'description' => 'Описание заметки_0',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function orderByNameDescProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'count' => 2,
                    'page' => 1,
                    'total' => 2,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                        [
                            'name' => 'Заметка_0',
                            'description' => 'Описание заметки_0',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function orderByCreatedAtAscProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'count' => 2,
                    'page' => 1,
                    'total' => 2,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                        [
                            'name' => 'Заметка_0',
                            'description' => 'Описание заметки_0',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function orderByCreatedAtDescProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                    ],
                ],
                'response' => [
                    'count' => 2,
                    'page' => 1,
                    'total' => 2,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                        [
                            'name' => 'Заметка_0',
                            'description' => 'Описание заметки_0',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function orderByUpdatedAtAscProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        'updated_at' => new \DateTimeImmutable('01.01.2025'),
                    ],
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        'updated_at' => new \DateTimeImmutable('01.01.2030'),
                    ],
                ],
                'response' => [
                    'count' => 2,
                    'page' => 1,
                    'total' => 2,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                        [
                            'name' => 'Заметка_0',
                            'description' => 'Описание заметки_0',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function orderByUpdatedAtDescProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        'updated_at' => new \DateTimeImmutable('01.01.2030'),
                    ],
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        'updated_at' => new \DateTimeImmutable('01.01.2025'),
                    ],
                ],
                'response' => [
                    'count' => 2,
                    'page' => 1,
                    'total' => 2,
                    'pages' => 1,
                    'items' => [
                        [
                            'name' => 'Заметка_1',
                            'description' => 'Описание заметки_1',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                        [
                            'name' => 'Заметка_0',
                            'description' => 'Описание заметки_0',
                            'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                        ],
                    ],
                ],
            ],
        ];
    }
}
