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
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') return false;
            self::login($user);
            return true;
        }
        return false;
    }

    public static function login(array $user): void
    {
        $session = new Session();
        $session->regenerate();
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
        self::$user = $db->fetch(
            "SELECT * FROM users WHERE id = ? AND status = 'active' AND deleted_at IS NULL",
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
            $view = new View();
            $view->render('errors/403', [], 'main');
            exit;
        }
    }

    public static function requireModerator(): void
    {
        self::requireLogin();
        if (!self::isModerator()) {
            http_response_code(403);
            $view = new View();
            $view->render('errors/403', [], 'main');
            exit;
        }
    }
}
