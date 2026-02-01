<?php

declare(strict_types=1);

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class SecurityService
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {}

    public function getBearerToken(Request $request): ?string
    {
        $token = $request->headers->get(key: 'Authorization');

        if ($token && preg_match(pattern: '/^Bearer\s+\S+/', subject: $token)) {
            return substr(string: $token, offset: 7);
        }

        return null;
    }

    public function isValidJwtToken(string $token): bool
    {
        try {
            $this->jwtManager->parse(token: $token);

            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
