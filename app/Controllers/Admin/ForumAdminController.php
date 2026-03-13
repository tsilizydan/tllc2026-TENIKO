<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class ForumAdminController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $forums = $this->db()->fetchAll(
            "SELECT f.*, COUNT(t.id) AS topic_count_live
             FROM forums f
             LEFT JOIN topics t ON t.forum_id = f.id AND t.deleted_at IS NULL
             GROUP BY f.id ORDER BY f.sort_order, f.name"
        );
        $this->render('admin/forums/index', [
            'forums'    => $forums,
            'pageTitle' => 'Forums Admin — TENIKO',
        ], 'admin');
    }

    public function storeChannel(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $name = trim($request->post('name', ''));
        if (!$name) { $this->session->flash('error', 'Name required.'); $this->redirect('/admin/forums'); }
        $slug = slug($name) . '-' . time();
        $this->db()->insert('forums', [
            'name'        => $name,
            'slug'        => $slug,
            'description' => trim($request->post('description', '')),
            'sort_order'  => (int)$request->post('sort_order', 0),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        $this->session->flash('success', 'Forum channel created.');
        $this->redirect('/admin/forums');
    }

    public function updateChannel(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->update('forums', [
            'name'        => trim($request->post('name', '')),
            'description' => trim($request->post('description', '')),
            'sort_order'  => (int)$request->post('sort_order', 0),
        ], 'id = :id', ['id' => $id]);
        $this->session->flash('success', 'Forum updated.');
        $this->redirect('/admin/forums');
    }

    public function destroyChannel(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->delete('forums', 'id = ?', [$id]);
        $this->session->flash('success', 'Forum deleted.');
        $this->redirect('/admin/forums');
    }

    public function topics(Request $request): void
    {
        $this->requireAdmin();
        $db     = $this->db();
        $page   = max(1, (int)$request->get('page', 1));
        $limit  = 25; $offset = ($page - 1) * $limit;
        $status = $request->get('status', '');

        $where  = "t.deleted_at IS NULL";
        $params = [];
        if ($status) { $where .= " AND t.status = :status"; $params['status'] = $status; }

        $total  = $db->count("SELECT COUNT(*) FROM topics t WHERE {$where}", $params);
        $topics = $db->fetchAll(
            "SELECT t.id, t.title, t.status, t.created_at, t.reply_count, t.view_count,
                    f.name AS forum_name, u.username
             FROM topics t
             LEFT JOIN forums f ON f.id = t.forum_id
             LEFT JOIN users  u ON u.id = t.user_id
             WHERE {$where}
             ORDER BY t.created_at DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );

        $this->render('admin/forums/topics', [
            'topics'    => $topics,
            'status'    => $status,
            'total'     => $total,
            'page'      => $page,
            'last_page' => (int)ceil($total / $limit),
            'pageTitle' => 'Forum Topics — TENIKO Admin',
        ], 'admin');
    }

    public function topicAction(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id     = (int)$request->param('id');
        $action = $request->post('action', '');

        $map = ['pin' => 'pinned', 'close' => 'closed', 'open' => 'open', 'archive' => 'archived'];
        if (isset($map[$action])) {
            $this->db()->update('topics', ['status' => $map[$action]], 'id = :id', ['id' => $id]);
            $this->session->flash('success', "Topic " . ucfirst($action) . "d.");
        } elseif ($action === 'delete') {
            $this->db()->update('topics', ['deleted_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $id]);
            $this->session->flash('success', 'Topic deleted.');
        }
        $this->redirect('/admin/forums/topics');
    }
}
