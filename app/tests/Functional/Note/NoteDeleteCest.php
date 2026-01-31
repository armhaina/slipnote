<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\Entity\Note;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\NoteFixture;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\Handle\HandleAuthorizedTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedAuthorizationTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedForbiddenTrait;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Symfony\Component\HttpFoundation\Request;

final class NoteDeleteCest extends AbstractCest
{
    use TestFailedAuthorizationTrait;
    use TestFailedForbiddenTrait;
    use HandleAuthorizedTrait;

    private const string URL = '/api/v1/notes';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('DELETE/200: Удалить заметку');

        $this->authorized(I: $I);
        $entity = self::getEntity(I: $I, fixtures: $example['fixtures']);

        $I->sendDelete(url: self::URL.'/'.$entity->getId());
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->dontSeeInRepository(entity: Note::class, params: ['name' => $entity->getName()]);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected static function getMethod(): string
    {
        return Request::METHOD_DELETE;
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        if (!empty($context['entity'])) {
            return self::URL.'/'.$context['entity']->getId();
        }

        return self::URL.'/'.self::getEntity(I: $I)->getId();
    }

    protected function mainProvider(): array
    {
        return [
            [
                'fixtures' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание_0',
                    'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                ],
                'response' => [
                    'success' => true,
                    'message' => 'Запись успешно удалена',
                ],
            ],
        ];
    }

    private static function getEntity(FunctionalTester $I, array $fixtures = []): Note
    {
        return NoteFixture::load(I: $I, data: $fixtures);
    }
}
