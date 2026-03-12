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
        return (string)$session->get(self::TOKEN_KEY);
    }

    public static function validate(?string $token): bool
    {
        if (empty($token)) return false;
        $session = new Session();
        $session->start();
        $stored = $session->get(self::TOKEN_KEY);
        if (!$stored) return false;
        return hash_equals((string)$stored, $token);
    }

    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
    }

    /**
     * Verify CSRF for POST/PUT/PATCH/DELETE requests.
     * For AJAX requests: returns JSON 419.
     * For normal form submissions: flashes an error and redirects back.
     */
    public static function verify(Request $request): void
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return;
        }
        $token = $request->post('_csrf_token')
              ?? $request->header('X-CSRF-Token')
              ?? $request->header('X-Csrf-Token');

        if (self::validate($token)) {
            return; // Valid — proceed
        }

        // Determine if AJAX / JSON request
        $acceptsJson = str_contains($request->header('Accept') ?? '', 'application/json');
        $isXhr       = strtolower($request->header('X-Requested-With') ?? '') === 'xmlhttprequest';

        if ($isXhr || $acceptsJson) {
            http_response_code(419);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['error' => 'CSRF token mismatch. Please refresh and try again.']);
            exit;
        }

        // Normal form: flash and redirect back
        $session = new Session();
        $session->start();
        $session->flash('error', 'Your session expired. Please try again.');
        // Use path from referer, or '/' as fallback — avoid absolute-URL redirect issues
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $path = parse_url($referer, PHP_URL_PATH) ?: '/';
        header('Location: ' . $path, true, 303);
        exit;
    }
}
