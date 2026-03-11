<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected bool $softDelete = true;

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL", [$email]
        );
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE username = ? AND deleted_at IS NULL", [$username]
        );
    }

    public function register(array $data): int|string
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['email_verify_token'] = bin2hex(random_bytes(32));
        $data['status'] = 'pending';
        $data['role']   = 'user';
        return $this->create($data);
    }

    public function verify(string $token): bool
    {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email_verify_token = ? AND email_verified_at IS NULL", [$token]
        );
        if (!$user) return false;
        $this->update($user['id'], [
            'email_verified_at'   => date('Y-m-d H:i:s'),
            'email_verify_token'  => null,
            'status'              => 'active',
        ]);
        return true;
    }

    public function getPublicProfile(string $username): ?array
    {
        $user = $this->db->fetch(
            "SELECT id, username, display_name, avatar, bio, role, reputation, created_at
             FROM users WHERE username = ? AND status = 'active' AND deleted_at IS NULL", [$username]
        );
        if (!$user) return null;
        $user['badges'] = $this->db->fetchAll(
            "SELECT b.* FROM user_badges ub JOIN badges b ON b.id = ub.badge_id WHERE ub.user_id = ?",
            [$user['id']]
        );
        $user['word_contributions'] = $this->db->count(
            "SELECT COUNT(*) FROM words WHERE created_by = ? AND status = 'published'", [$user['id']]
        );
        $user['proverb_contributions'] = $this->db->count(
            "SELECT COUNT(*) FROM proverbs WHERE created_by = ? AND status = 'published'", [$user['id']]
        );
        return $user;
    }
}
