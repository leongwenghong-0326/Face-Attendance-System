<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(Request $request): bool
    {
        $user = Session::get('user');

        if (!$user || !in_array($user['role'], $this->allowedRoles, true)) {
            http_response_code(403);
            if ($request->isAjax()) {
                (new Response())->json([
                    'error' => 'Forbidden',
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            } else {
                View::render('errors/403', [
                    'title' => '403 Forbidden - Access Denied',
                    'message' => 'You do not have the required permissions to access this page.'
                ], 'layouts/auth');
            }
            return false;
        }

        return true;
    }
}
