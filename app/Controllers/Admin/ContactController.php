<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class ContactController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $db     = $this->db();
        $status = $request->get('status', '');
        $page   = max(1, (int)$request->get('page', 1));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $where  = '1=1';
        $params = [];
        if ($status) {
            $where .= ' AND status = :status';
            $params['status'] = $status;
        }

        $total   = $db->count("SELECT COUNT(*) FROM contact_messages WHERE {$where}", $params);
        $messages = $db->fetchAll(
            "SELECT * FROM contact_messages WHERE {$where} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}",
            $params
        );

        // Count unread
        $unread = $db->count("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");

        $this->render('admin/contact/index', [
            'messages'   => $messages,
            'status'     => $status,
            'unread'     => $unread,
            'total'      => $total,
            'page'       => $page,
            'last_page'  => (int)ceil($total / $limit),
            'pageTitle'  => 'Contact Inbox — TENIKO Admin',
        ], 'admin');
    }

    public function show(Request $request): void
    {
        $this->requireAdmin();
        $id  = (int)$request->param('id');
        $msg = $this->db()->fetch("SELECT * FROM contact_messages WHERE id = ?", [$id]);
        if (!$msg) { $this->abort(404); return; }

        // Mark as read
        if ($msg['status'] === 'unread') {
            $this->db()->update('contact_messages', ['status' => 'read'], 'id = :id', ['id' => $id]);
            $msg['status'] = 'read';
        }

        $csrfToken = \App\Core\CSRF::generate();
        $this->render('admin/contact/show', [
            'msg'       => $msg,
            'csrfToken' => $csrfToken,
            'pageTitle' => 'Message from ' . ($msg['name'] ?? 'Unknown'),
        ], 'admin');
    }

    public function reply(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id    = (int)$request->param('id');
        $reply = trim($request->post('reply', ''));
        $msg   = $this->db()->fetch("SELECT * FROM contact_messages WHERE id = ?", [$id]);
        if (!$msg) { $this->abort(404); return; }

        // Store reply in DB (in production, also send email here)
        $this->db()->update('contact_messages', [
            'reply'      => $reply,
            'status'     => 'replied',
            'replied_by' => \App\Core\Auth::id(),
            'replied_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $id]);

        $this->session->flash('success', 'Reply saved. Remember to send the email manually if email is not configured.');
        $this->redirect('/admin/contact/' . $id);
    }

    public function archive(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->update('contact_messages', ['status' => 'archived'], 'id = :id', ['id' => $id]);
        $this->session->flash('success', 'Message archived.');
        $this->redirect('/admin/contact');
    }

    public function destroy(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->delete('contact_messages', 'id = ?', [$id]);
        $this->session->flash('success', 'Message deleted.');
        $this->redirect('/admin/contact');
    }
}
