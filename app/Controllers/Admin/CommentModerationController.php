<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class CommentModerationController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $db     = $this->db();
        $status = $request->get('status', 'published');
        $page   = max(1, (int)$request->get('page', 1));
        $limit  = 30;
        $offset = ($page - 1) * $limit;

        $params = ['status' => $status];
        $total  = $db->count("SELECT COUNT(*) FROM comments WHERE status = :status AND deleted_at IS NULL", $params);
        $comments = $db->fetchAll(
            "SELECT c.*, u.username, u.display_name
             FROM comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.status = :status AND c.deleted_at IS NULL
             ORDER BY c.created_at DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );

        $this->render('admin/comments/index', [
            'comments'  => $comments,
            'status'    => $status,
            'total'     => $total,
            'page'      => $page,
            'last_page' => (int)ceil($total / $limit),
            'pageTitle' => 'Comment Moderation — TENIKO Admin',
        ], 'admin');
    }

    public function hide(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->update('comments', ['status' => 'hidden'], 'id = :id', ['id' => $id]);
        $this->session->flash('success', 'Comment hidden.');
        $this->redirect('/admin/comments');
    }

    public function restore(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->update('comments', ['status' => 'published'], 'id = :id', ['id' => $id]);
        $this->session->flash('success', 'Comment restored.');
        $this->redirect('/admin/comments?status=hidden');
    }

    public function destroy(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->update('comments', ['deleted_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $id]);
        $this->session->flash('success', 'Comment deleted.');
        $this->redirect('/admin/comments');
    }
}
