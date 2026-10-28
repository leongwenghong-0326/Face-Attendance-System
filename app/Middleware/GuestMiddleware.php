<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class GuestMiddleware
{
    public function handle(Request $request): bool
    {
        if (Session::has('user')) {
            $user = Session::get('user');
            $role = $user['role'] ?? 'student';

            $target = match ($role) {
                'admin' => '/admin/dashboard',
                'teacher' => '/teacher/dashboard',
                'student' => '/student/dashboard',
                default => '/login',
            };

            (new Response())->redirect($target);
            return false;
        }

        return true;
    }
}
