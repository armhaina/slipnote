<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\DataFixtures\Note\NoteListFixtures;
use App\DataFixtures\UserAuthorizedFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

final class NoteCreateCest extends AbstractCest
{
    #[DataProvider('successProvider')]
    public function tryToTest(FunctionalTester $I, Example $example): void
    {
        $this->authorized(I: $I);

        $I->sendPost(url: '/api/v1/notes', params: $example['request']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function successProvider(): array
    {
        return [
            [
                'groups' => NoteListFixtures::GROUPS,
                'request' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                    'isPrivate' => true,
                ],
                'response' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                    'isPrivate' => true,
                    'user' => ['email' => UserAuthorizedFixtures::EMAIL],
                ],
            ],
        ];
    }
}
