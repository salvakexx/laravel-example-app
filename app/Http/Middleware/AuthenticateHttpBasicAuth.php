<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateHttpBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->authenticate($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    protected function authenticate(Request $request): bool
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Basic ')) {
            return false;
        }

        [$username, $password] = explode(':', base64_decode(substr($authHeader, 6)), 2);

        if (!$username || !$password) {
            return false;
        }

        return $this->validateUser($username, $password);
    }

    protected function validateUser(string $username, string $password): bool
    {
        $credentialsData = config('auth.http_basic_auth_credentials')[$username] ?? null;

        if ($credentialsData !== null) {
            return $credentialsData['password'] === $password;
        }

        return false;
    }
}
