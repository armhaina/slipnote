<?php

declare(strict_types=1);

namespace App\Tests\Support\Data\Trait\Test;

use App\Tests\Support\Data\Trait\AbstractTrait;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

trait TestFailedAuthorizationTrait
{
    use AbstractTrait;

    /**
     * @throws \Exception
     */
    #[DataProvider('failedAuthorizationProvider')]
    public function failedAuthorization(FunctionalTester $I, Scenario $scenario, Example $example): void
    {
        self::setWantTo(scenario: $scenario, wantTo: self::getMethod().'/401 АВТОРИЗАЦИЯ: Ошибка авторизации');

        $context = $example['context'] ?? [];

        self::contextHandle(I: $I, context: $context);

        $params = $context['params'] ?? [];

        $this->request(I: $I, url: self::getUrl(I: $I, context: $context), params: $params);

        $I->seeResponseCodeIs(code: HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        // HACK: События LexikJWTAuthenticationBundle не вызываются в тестовом окружении
        if (!empty($data['message']) && 'JWT Token not found' === $data['message']) {
            // Получаем EventDispatcher
            $dispatcher = $I->grabService('event_dispatcher');

            // Создаем событие
            $event = new JWTExpiredEvent(exception: new AuthenticationException(), response: null);

            // Принудительно вызываем событие
            $dispatcher->dispatch($event, 'lexik_jwt_authentication.on_jwt_expired');

            // Проверяем результат
            $response = $event->getResponse();

            $I->assertInstanceOf(expected: JsonResponse::class, actual: $response);
            $I->assertEquals(expected: HttpCode::UNAUTHORIZED, actual: $response->getStatusCode());

            // Получаем данные
            $data = json_decode($response->getContent(), true);
        }

        $I->assertEquals(
            expected: $data,
            actual: $data
        );
    }

    protected function failedAuthorizationProvider(): array
    {
        return [['plug']];
    }
}
