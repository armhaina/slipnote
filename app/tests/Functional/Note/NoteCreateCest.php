<?php

declare(strict_types=1);

namespace App\Tests\Functional\Note;

use App\Tests\Functional\AbstractCest;
use App\Tests\Support\Data\Fixture\UserFixture;
use App\Tests\Support\Data\Trait\AbstractTrait;
use App\Tests\Support\Data\Trait\Handle\HandleAuthorizedTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedAuthorizationTrait;
use App\Tests\Support\Data\Trait\Test\TestFailedValidationTrait;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;

final class NoteCreateCest extends AbstractCest
{
    use AbstractTrait;
    use HandleAuthorizedTrait;
    use TestFailedAuthorizationTrait;
    use TestFailedValidationTrait;

    private const string URL = '/api/v1/notes';

    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('POST/200: Создать заметку');

        $this->authorized(I: $I);

        $I->sendPost(url: self::URL, params: $example['params']);
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected static function getUrl(FunctionalTester $I, array $context = []): string
    {
        return self::URL;
    }

    protected static function getMethod(): string
    {
        return Request::METHOD_POST;
    }

    protected function mainProvider(): array
    {
        return [
            [
                'params' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                ],
                'response' => [
                    'name' => 'Заметка_0',
                    'description' => 'Описание заметки_0',
                    'is_trashed' => false,
                    'user' => ['email' => UserFixture::USER_AUTHORIZED_EMAIL],
                ],
            ],
        ];
    }

    protected function failedValidationProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                'want' => 'POST/422: Название (мин.)',
                'params' => [
                    'name' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(0, 0).'}'),
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'name',
                            'message' => 'Поле не может быть пустым',
                        ],
                        [
                            'property' => 'name',
                            'message' => 'Минимально допустимое значение символов: 1. Ваше кол-во символов: 0',
                        ],
                    ],
                ],
            ],
            [
                'want' => 'POST/422: Название (макс.)',
                'params' => [
                    'name' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(101, 101).'}'),
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'name',
                            'message' => 'Максимально допустимое значение символов: 100. Ваше кол-во символов: 101',
                        ],
                    ],
                ],
            ],
            [
                'want' => 'POST/422: Описание (макс.)',
                'params' => [
                    'name' => 'Название',
                    'description' => $faker->regexify('[A-Za-z0-9]{'.mt_rand(10001, 10001).'}'),
                ],
                'response' => [
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => [
                        [
                            'property' => 'description',
                            'message' => 'Максимально допустимое значение символов: 10000. Ваше кол-во символов: 10001',
                        ],
                    ],
                ],
            ],
        ];
    }
}
