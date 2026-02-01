<?php

declare(strict_types=1);

namespace App\Tests\Support\Data\Trait\Handle;

use App\Entity\User;
use App\Enum\Entity\User\RoleUser;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\AbstractTrait;
use App\Tests\Support\FunctionalTester;
use Codeception\Util\HttpCode;

trait HandleAuthorizedTrait
{
    use AbstractTrait;

    protected function authorized(FunctionalTester $I): User
    {
        $user = UserFixture::load(I: $I, data: [
            'email' => UserFixture::USER_AUTHORIZED_EMAIL,
            'password' => UserFixture::USER_AUTHORIZED_PASSWORD,
            'roles' => [RoleUser::ROLE_USER->value],
        ]);

        $I->sendPost(
            url: '/api/login_check',
            params: [
                'username' => UserFixture::USER_AUTHORIZED_EMAIL,
                'password' => UserFixture::USER_AUTHORIZED_PASSWORD,
            ]
        );
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $I->haveHttpHeader(
            name: 'Authorization',
            value: 'Bearer '.json_decode($I->grabResponse(), true)['token']
        );

        return $user;
    }
}
