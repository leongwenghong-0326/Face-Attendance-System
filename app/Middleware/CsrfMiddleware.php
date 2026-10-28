<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Helpers\Csrf;

class CsrfMiddleware
{
    public function handle(Request $request): bool
    {
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $token = $request->input('_token');

            if (!$token && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
            }

            if (!Csrf::validate($token)) {
                http_response_code(403);
                if ($request->isAjax()) {
                    (new Response())->json([
                        'error' => 'CSRF Token Mismatch',
                        'message' => 'Invalid or expired session security token. Please refresh and try again.'
                    ], 403);
                } else {
                    Session::setFlash('error', 'Security token expired. Please try again.');
                    (new Response())->redirect($_SERVER['HTTP_REFERER'] ?? '/login');
                }
                return false;
            }
        }

        return true;
    }
}
