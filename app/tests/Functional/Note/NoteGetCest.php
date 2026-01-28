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

final class NoteGetCest extends AbstractCest
{
    private const string URL = '/api/v1/notes';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/200: Получить заметку');

        $this->authorized(I: $I);
        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendGet(url: self::URL.'/'.$note->getId());
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

        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendGet(url: self::URL.'/'.$note->getId());
        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    #[DataProvider('forbiddenProvider')]
    public function forbidden(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('GET/403: Доступ запрещен');

        $this->authorized(I: $I);
        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendGet(url: self::URL.'/'.$note->getId());
        $I->seeResponseCodeIs(code: HttpCode::FORBIDDEN);
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
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
                'response' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
            ],
        ];
    }

    protected function failedAuthorizationProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
                'response' => [
                    'code' => 401,
                    'message' => 'JWT Token not found',
                ],
            ],
        ];
    }

    protected function forbiddenProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => 'test_0@mail.ru'],
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Доступ запрещен',
                ],
            ],
        ];
    }
}
