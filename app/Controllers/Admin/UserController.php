<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class UserController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $page   = max(1, (int)$request->get('page', 1));
        $q      = $request->get('q', '');
        $role   = $request->get('role', '');

        $where  = "deleted_at IS NULL";
        $params = [];
        if ($q)    { $where .= " AND (username LIKE :q OR email LIKE :q)"; $params['q'] = "%{$q}%"; }
        if ($role) { $where .= " AND role=:role"; $params['role'] = $role; }

        $db    = $this->db();
        $total = $db->count("SELECT COUNT(*) FROM users WHERE {$where}", $params);
        $limit = 25; $offset = ($page - 1) * $limit;
        $users = $db->fetchAll(
            "SELECT id, username, email, display_name, role, status, reputation, created_at
             FROM users WHERE {$where} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}",
            $params
        );
        $badges = $db->fetchAll("SELECT id, name FROM badges ORDER BY name");

        $this->render('admin/users/index', [
            'users'   => $users,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $limit,
            'q'       => $q,
            'role'    => $role,
            'badges'  => $badges,
            'pageTitle' => 'Manage Users — TENIKO Admin',
        ], 'admin');
    }

    public function show(Request $request): void
    {
        $this->requireAdmin();
        $id   = (int)$request->param('id');
        $user = $this->db()->fetch("SELECT * FROM users WHERE id=?", [$id]);
        if (!$user) { $this->abort(404); return; }
        $user['badges'] = $this->db()->fetchAll("SELECT b.* FROM user_badges ub JOIN badges b ON b.id=ub.badge_id WHERE ub.user_id=?", [$id]);
        $this->render('admin/users/show', ['profile' => $user, 'pageTitle' => 'User Detail'], 'admin');
    }

    public function updateRole(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id   = (int)$request->param('id');
        $role = $request->post('role', 'user');
        $allowed = ['admin', 'moderator', 'contributor', 'user'];
        if (!in_array($role, $allowed)) { $this->json(['error' => 'Invalid role.'], 400); }
        $this->db()->update('users', ['role' => $role, 'updated_at' => date('Y-m-d H:i:s')], 'id=:id', ['id' => $id]);
        $this->json(['success' => true, 'role' => $role]);
    }

    public function updateStatus(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id     = (int)$request->param('id');
        $status = $request->post('status', 'active');
        $allowed = ['active', 'suspended', 'banned'];
        if (!in_array($status, $allowed)) { $this->json(['error' => 'Invalid status.'], 400); }
        $this->db()->update('users', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], 'id=:id', ['id' => $id]);
        $this->json(['success' => true, 'status' => $status]);
    }

    public function awardBadge(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $userId  = (int)$request->param('id');
        $badgeId = (int)$request->post('badge_id');
        $exists = $this->db()->fetch("SELECT * FROM user_badges WHERE user_id=? AND badge_id=?", [$userId, $badgeId]);
        if (!$exists) {
            $this->db()->insert('user_badges', ['user_id' => $userId, 'badge_id' => $badgeId, 'awarded_at' => date('Y-m-d H:i:s')]);
        }
        $this->json(['success' => true]);
    }
}
