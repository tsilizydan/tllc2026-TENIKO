<?php
declare(strict_types=1);
/**
 * TENIKO — Global Helper Functions
 */

if (!function_exists('e')) {
    /**
     * HTML-escape a string (XSS prevention).
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /**
     * Build an absolute URL.
     */
    function url(string $path = '', array $query = []): string
    {
        $base = rtrim(env('APP_URL', ''), '/');
        $path = '/' . ltrim($path, '/');
        if (!empty($query)) {
            $path .= '?' . http_build_query($query);
        }
        return $base . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Return the URL to a public asset.
     */
    function asset(string $path): string
    {
        return url('/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('slug')) {
    /**
     * Convert a string to a URL-safe slug.
     */
    function slug(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? $text;
        return trim($text, '-');
    }
}

if (!function_exists('truncate')) {
    /**
     * Truncate a string to a max number of characters, appending an ellipsis.
     */
    function truncate(string $text, int $length = 100, string $append = '…'): string
    {
        $text = strip_tags($text);
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $append;
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Return a human-readable "time ago" string.
     */
    function timeAgo(string|int $datetime): string
    {
        $time = is_int($datetime) ? $datetime : strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60)     return 'Just now';
        if ($diff < 3600)   return floor($diff / 60) . 'm ago';
        if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        if ($diff < 2592000) return floor($diff / 604800) . 'w ago';
        return date('M j, Y', $time);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format a datetime string (or timestamp) for human display.
     */
    function formatDate(string|int|null $datetime, string $format = 'M j, Y'): string
    {
        if (!$datetime) return '—';
        $time = is_int($datetime) ? $datetime : strtotime($datetime);
        return date($format, $time ?: time());
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render a hidden CSRF input field.
     */
    function csrf_field(): string
    {
        $token = \App\Core\CSRF::generate();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('env')) {
    /**
     * Read from loaded .env / environment variables.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump-and-die (debug helper).
     */
    function dd(mixed ...$vars): never
    {
        echo '<pre style="background:#1a1a1a;color:#e8e8e8;padding:1.5rem;border-radius:8px;font-size:13px;overflow:auto;margin:1rem">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }
}

if (!function_exists('redirect')) {
    /**
     * Perform an HTTP redirect.
     */
    function redirect(string $path, int $code = 302): never
    {
        $location = str_starts_with($path, 'http') ? $path : url($path);
        header('Location: ' . $location, true, $code);
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with an HTTP error.
     */
    function abort(int $code = 404, string $message = ''): never
    {
        http_response_code($code);
        $view = new \App\Core\View();
        $view->render('errors/error', ['code' => $code, 'message' => $message]);
        exit;
    }
}

if (!function_exists('number_shorten')) {
    /**
     * Shorten a large number: 1500 → 1.5K, 1200000 → 1.2M.
     */
    function number_shorten(int $n): string
    {
        if ($n >= 1_000_000) return round($n / 1_000_000, 1) . 'M';
        if ($n >= 1_000)     return round($n / 1_000, 1)     . 'K';
        return (string)$n;
    }
}

if (!function_exists('isProd')) {
    function isProd(): bool
    {
        return (env('APP_ENV', 'local') === 'production');
    }
}
