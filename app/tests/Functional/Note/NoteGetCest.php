<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\DataFixtures\Note\NoteGetFixtures;
use App\DataFixtures\Note\NoteListFixtures;
use App\Entity\Note;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

final class NoteGetCest extends AbstractCest
{
    #[DataProvider('successProvider')]
    public function tryToTest(FunctionalTester $I, Example $example): void
    {
        $user = $this->authorizedUpdate(I: $I);

        $note = $this->fixturesLoadUpdate(
            I: $I,
            entityClass: Note::class,
            data: array_merge($example['fixtures'], ['user' => $user])
        );

        $I->sendGet(url: '/api/v1/notes/' . $note->getId());
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
                'groups' => NoteGetFixtures::GROUPS,
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'isPrivate' => true,
                ],
                'response' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'isPrivate' => true,
                    'user' => ['email' => self::USER_EMAIL],
                ],
            ],
        ];
    }
}
