<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;

abstract class AbstractCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }
}
