<?php
declare(strict_types=1);
namespace App\Core;

class Auth
{
    private static ?array $user = null;

    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL",
            [$email]
        );
        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;
        // Accept 'active' or 'pending' users — 'suspended'/'banned' are blocked
        if (in_array($user['status'], ['suspended', 'banned'])) return false;
        self::login($user);
        return true;
    }

    public static function login(array $user): void
    {
        $session = new Session();
        $session->start();
        // Preserve CSRF token before session ID regeneration
        $csrfToken = $session->get('_csrf_token');
        $session->regenerate();
        if ($csrfToken) {
            $session->set('_csrf_token', $csrfToken);
        }
        $session->set('user_id', $user['id']);
        $session->set('user_role', $user['role']);
        self::$user = $user;

        // Update last login
        $db = Database::getInstance();
        $db->update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
    }

    public static function logout(): void
    {
        $session = new Session();
        $session->destroy();
        self::$user = null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function user(): ?array
    {
        if (self::$user !== null) return self::$user;
        $session = new Session();
        $session->start();
        $userId = $session->get('user_id');
        if (!$userId) return null;
        $db = Database::getInstance();
        // Accept active and pending users (suspended/banned are excluded)
        self::$user = $db->fetch(
            "SELECT * FROM users WHERE id = ? AND status IN ('active','pending') AND deleted_at IS NULL",
            [$userId]
        ) ?: null;
        return self::$user;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function role(): string
    {
        return self::user()['role'] ?? 'guest';
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isModerator(): bool
    {
        return in_array(self::role(), ['admin', 'moderator']);
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            $session = new Session();
            $session->flash('error', 'Please log in to continue.');
            header('Location: /login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            try {
                $view = new View();
                $view->render('errors/error', ['code' => 403, 'message' => 'Admin access required.']);
            } catch (\Throwable) {
                echo '<h1>403 — Forbidden</h1><p><a href="/">Go Home</a></p>';
            }
            exit;
        }
    }

    public static function requireModerator(): void
    {
        self::requireLogin();
        if (!self::isModerator()) {
            http_response_code(403);
            try {
                $view = new View();
                $view->render('errors/error', ['code' => 403, 'message' => 'Moderator access required.']);
            } catch (\Throwable) {
                echo '<h1>403 — Forbidden</h1><p><a href="/">Go Home</a></p>';
            }
            exit;
        }
    }
}
