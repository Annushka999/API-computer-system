<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    public function render(Request $request, \Exception $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => 'your error message'
            ],401);
        }

        return parent::render($request, $exception);
    }
}
