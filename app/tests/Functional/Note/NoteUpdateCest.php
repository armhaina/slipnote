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

final class NoteUpdateCest extends AbstractCest
{
    #[DataProvider('successProvider')]
    public function tryToTest(FunctionalTester $I, Example $example): void
    {
        $this->authorized(I: $I);
        $note = NoteFixtures::load(I: $I, data: $example['fixtures']);

        $I->sendPut(url: '/api/v1/notes/' . $note->getId(), params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function successProvider(): array
    {
        return [
            'main' => [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
                'request' => [
                    'name' => 'Заметка_1',
                    'description' => 'Описание заметки_1',
                ],
                'response' => [
                    'name' => 'Заметка_1',
                    'description' => 'Описание заметки_1',
                    'user' => ['email' => UserFixtures::USER_AUTHORIZED_EMAIL],
                ],
            ],
        ];
    }
}
