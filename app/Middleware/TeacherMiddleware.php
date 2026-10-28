<?php
namespace App\Middleware;

use App\Core\Request;

class TeacherMiddleware
{
    public function handle(Request $request): bool
    {
        $roleMiddleware = new RoleMiddleware(['teacher']);
        return $roleMiddleware->handle($request);
    }
}
