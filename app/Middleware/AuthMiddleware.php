<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        if (!Session::has('user')) {
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Unauthenticated', 'message' => 'Please log in to continue.'], 401);
                return false;
            }

            Session::setFlash('error', 'Please log in to access this page.');
            (new Response())->redirect('/login');
            return false;
        }

        return true;
    }
}
