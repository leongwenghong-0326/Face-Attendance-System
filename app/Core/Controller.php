<?php
namespace App\Core;

use App\Helpers\Flash;
use App\Helpers\Validator;
use App\Services\AuditService;

abstract class Controller
{
    protected function view(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($viewPath, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        (new Response())->json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): void
    {
        (new Response())->redirect($url, $status);
    }

    protected function flash(string $type, string $message): void
    {
        Session::setFlash($type, $message);
    }

    protected function user(): ?array
    {
        return Session::get('user');
    }

    protected function userId(): ?int
    {
        return Session::get('user')['id'] ?? null;
    }

    protected function validate(array $data, array $rules): array
    {
        $validator = new Validator($data, $rules);
        if (!$validator->validate()) {
            return $validator->errors();
        }
        return [];
    }
}
