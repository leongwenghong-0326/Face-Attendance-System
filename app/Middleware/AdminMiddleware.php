<?php
namespace App\Middleware;

use App\Core\Request;

class AdminMiddleware
{
    public function handle(Request $request): bool
    {
        $roleMiddleware = new RoleMiddleware(['admin']);
        return $roleMiddleware->handle($request);
    }
}
