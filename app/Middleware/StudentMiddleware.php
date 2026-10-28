<?php
namespace App\Middleware;

use App\Core\Request;

class StudentMiddleware
{
    public function handle(Request $request): bool
    {
        $roleMiddleware = new RoleMiddleware(['student']);
        return $roleMiddleware->handle($request);
    }
}
