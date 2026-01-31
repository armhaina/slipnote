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

trait TestFailedValidationTrait
{
    use AbstractTrait;
    use HandleAuthorizedTrait;

    /**
     * @throws \Exception
     */
    #[DataProvider('failedValidationProvider')]
    public function failedValidation(FunctionalTester $I, Scenario $scenario, Example $example): void
    {
        self::setWantTo(scenario: $scenario, wantTo: $example['want']);

        $this->authorized(I: $I);
        $this->request(I: $I, params: $example['params']);

        $I->seeResponseCodeIs(code: HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    abstract protected function failedValidationProvider(): array;
}
