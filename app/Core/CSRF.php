<?php
declare(strict_types=1);
namespace App\Core;

class CSRF
{
    private const TOKEN_KEY = '_csrf_token';

    public static function generate(): string
    {
        $session = new Session();
        $session->start();
        if (!$session->has(self::TOKEN_KEY)) {
            $session->set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        }
        return $session->get(self::TOKEN_KEY);
    }

    public static function validate(?string $token): bool
    {
        if (empty($token)) return false;
        $session = new Session();
        $session->start();
        $stored = $session->get(self::TOKEN_KEY);
        if (!$stored) return false;
        return hash_equals($stored, $token);
    }

    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
    }

    public static function verify(Request $request): void
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $request->post('_csrf_token') ?? $request->header('X-CSRF-Token');
            if (!self::validate($token)) {
                http_response_code(419);
                die(json_encode(['error' => 'CSRF token mismatch.']));
            }
        }
    }
}
