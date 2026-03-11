<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;

class UserController extends Controller
{
    public function profile(Request $request): void
    {
        $username = $request->param('username');
        $model    = new User();
        $user     = $model->getPublicProfile($username);
        if (!$user) { $this->abort(404, 'User not found.'); return; }

        $contributions = $this->db()->fetchAll(
            "SELECT 'word' AS type, word AS title, slug, created_at FROM words WHERE created_by=? AND status='published' AND deleted_at IS NULL
             UNION ALL
             SELECT 'proverb' AS type, LEFT(text,80) AS title, slug, created_at FROM proverbs WHERE created_by=? AND status='published' AND deleted_at IS NULL
             ORDER BY created_at DESC LIMIT 20",
            [$user['id'], $user['id']]
        );
        $topics = $this->db()->fetchAll(
            "SELECT t.id, t.title, t.slug, t.created_at FROM topics t WHERE t.user_id=? AND t.deleted_at IS NULL ORDER BY t.created_at DESC LIMIT 10",
            [$user['id']]
        );

        $this->render('user/profile', [
            'profile'       => $user,
            'contributions' => $contributions,
            'topics'        => $topics,
            'pageTitle'     => e($user['display_name'] ?? $user['username']) . ' — TENIKO',
        ]);
    }

    public function settings(Request $request): void
    {
        $this->requireLogin();
        $this->render('user/settings', ['pageTitle' => 'Account Settings — TENIKO']);
    }

    public function updateSettings(Request $request): void
    {
        $this->requireLogin();
        $this->verifyCsrf($request);
        $model = new User();
        $data  = [
            'display_name' => trim($request->post('display_name', '')),
            'bio'          => trim($request->post('bio', '')),
        ];
        if ($request->post('new_password')) {
            if (strlen($request->post('new_password')) < 8) {
                $this->session->flash('error', 'Password must be at least 8 characters.');
                $this->redirect('/settings');
            }
            $data['password'] = password_hash($request->post('new_password'), PASSWORD_BCRYPT, ['cost' => 12]);
        }
        $model->update(\App\Core\Auth::id(), $data);
        $this->session->flash('success', 'Settings updated successfully.');
        $this->redirect('/settings');
    }
}
