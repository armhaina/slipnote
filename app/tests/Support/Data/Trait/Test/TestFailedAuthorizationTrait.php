<?php

declare(strict_types=1);

namespace App\Tests\Support\Data\Trait\Test;

use App\Tests\Support\Data\Trait\AbstractTrait;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;

trait TestFailedAuthorizationTrait
{
    use AbstractTrait;

    /**
     * @throws \Exception
     */
    #[DataProvider('failedAuthorizationProvider')]
    public function failedAuthorization(FunctionalTester $I, Scenario $scenario, Example $example): void
    {
        self::setWantTo(scenario: $scenario, wantTo: self::getMethod().'/401: Ошибка авторизации');

        $this->request(I: $I);

        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function failedAuthorizationProvider(): array
    {
        return [
            [
                'response' => [
                    'code' => 401,
                    'message' => 'JWT Token not found',
                ],
            ],
        ];
    }
}
