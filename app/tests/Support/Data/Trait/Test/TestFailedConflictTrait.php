<?php

declare(strict_types=1);

namespace App\Tests\Support\Data\Trait\Test;

use App\Tests\Support\Data\Trait\AbstractTrait;
use App\Tests\Support\Data\Trait\Handle\HandleAuthorizedTrait;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;

trait TestFailedConflictTrait
{
    use AbstractTrait;
    use HandleAuthorizedTrait;

    /**
     * @throws \Exception
     */
    #[DataProvider('failedConflictProvider')]
    public function failedConflict(FunctionalTester $I, Scenario $scenario, Example $example): void
    {
        self::setWantTo(scenario: $scenario, wantTo: self::getMethod().'/409 КОНФЛИКТ: '.$example['want_to']);

        if (!empty($example['is_authorize']) && true === $example['is_authorize']) {
            $this->authorized(I: $I);
        }

        $context = $example['context'] ?? [];

        self::contextHandle(I: $I, context: $context);

        $params = $context['params'] ?? [];

        $this->request(I: $I, url: self::getUrl(I: $I, context: $context), params: $params);

        $I->seeResponseCodeIs(code: HttpCode::CONFLICT);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    abstract protected function failedConflictProvider(): array;
}
