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

trait TestSuccessTrait
{
    use AbstractTrait;
    use HandleAuthorizedTrait;

    /**
     * @throws \Exception
     */
    #[DataProvider('successProvider')]
    public function success(FunctionalTester $I, Scenario $scenario, Example $example): void
    {
        self::setWantTo(scenario: $scenario, wantTo: self::getMethod().'/200 УСПЕХ: '.$example['want_to']);

        if (!empty($example['is_authorize']) && true === $example['is_authorize']) {
            $this->authorized(I: $I);
        }

        $context = $example['context'] ?? [];

        self::contextHandle(I: $I, context: $context);

        $params = $context['params'] ?? [];

        $this->request(I: $I, url: self::getUrl(I: $I, context: $context), params: $params);

        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    abstract protected function successProvider(): array;
}
