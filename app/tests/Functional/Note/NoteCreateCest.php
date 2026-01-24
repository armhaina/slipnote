<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\Tests\_data\fixtures\UserFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

final class NoteCreateCest extends AbstractCest
{
    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('Создать заметку');

        $this->authorized(I: $I);

        $I->sendPost(url: '/api/v1/notes', params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function mainProvider(): array
    {
        return [
            [
                'request' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                ],
                'response' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
            ],
        ];
    }
}
