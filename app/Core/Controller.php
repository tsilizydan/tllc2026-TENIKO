<?php
declare(strict_types=1);
namespace App\Core;

abstract class Controller
{
    protected Session $session;
    protected Cache   $cache;

    public function __construct()
    {
        $this->session = new Session();
        $this->cache   = new Cache();
    }

    // ── Rendering ──────────────────────────────────────────

    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $v = new View();
        $v->render($view, $this->withFlash($data), $layout);
    }

    protected function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $path, int $code = 302): never
    {
        $location = str_starts_with($path, 'http') ? $path : (rtrim(env('APP_URL', ''), '/') . '/' . ltrim($path, '/'));
        header('Location: ' . $location, true, $code);
        exit;
    }

    protected function abort(int $code = 404, string $message = ''): void
    {
        http_response_code($code);
        $this->render('errors/error', ['code' => $code, 'message' => $message]);
        exit;
    }

    // ── Database ──────────────────────────────────────────

    protected function db(): Database
    {
        return Database::getInstance();
    }

    // ── Flash Messages ────────────────────────────────────

    private function withFlash(array $data): array
    {
        foreach (['success', 'error', 'info', 'warning'] as $type) {
            $data["flash_{$type}"] = $this->session->getFlash($type);
        }
        return $data;
    }

    // ── CSRF ──────────────────────────────────────────────

    protected function verifyCsrf(Request $request): void
    {
        CSRF::verify($request);
    }

    // ── Auth Guards ───────────────────────────────────────

    protected function requireLogin(): void
    {
        if (!Auth::check()) {
            $this->session->set('intended_url', $_SERVER['REQUEST_URI'] ?? '/');
            $this->session->flash('info', 'Please log in to continue.');
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if (!Auth::isAdmin()) {
            $this->abort(403, 'Admin access required.');
        }
    }

    protected function requireModerator(): void
    {
        $this->requireLogin();
        if (!Auth::isModerator()) {
            $this->abort(403, 'Moderator access required.');
        }
    }
}
