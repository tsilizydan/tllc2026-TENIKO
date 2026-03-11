<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class ModerationController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireModerator();
        $db = $this->db();
        $pendingWords   = $db->fetchAll("SELECT w.*, u.username FROM words w LEFT JOIN users u ON u.id=w.created_by WHERE w.status='pending' AND w.deleted_at IS NULL ORDER BY w.created_at DESC LIMIT 30");
        $pendingContrib = $db->fetchAll("SELECT c.*, u.username FROM contributions c LEFT JOIN users u ON u.id=c.user_id WHERE c.status='pending' ORDER BY c.created_at DESC LIMIT 30");
        $pendingComments= $db->fetchAll("SELECT cm.*, u.username FROM comments cm JOIN users u ON u.id=cm.user_id WHERE cm.status='pending' AND cm.deleted_at IS NULL ORDER BY cm.created_at DESC LIMIT 30");

        $this->render('admin/moderation', [
            'pendingWords'   => $pendingWords,
            'pendingContrib' => $pendingContrib,
            'pendingComments'=> $pendingComments,
            'pageTitle'      => 'Moderation — TENIKO Admin',
        ], 'admin');
    }

    public function approve(Request $request): void
    {
        $this->requireModerator();
        $this->verifyCsrf($request);
        $type = $request->post('type', '');
        $id   = (int)$request->post('id');
        if ($type === 'word') {
            $this->db()->update('words', ['status' => 'published'], 'id=:id', ['id' => $id]);
        } elseif ($type === 'contribution') {
            $this->db()->update('contributions', ['status' => 'approved', 'reviewed_by' => \App\Core\Auth::id(), 'reviewed_at' => date('Y-m-d H:i:s')], 'id=:id', ['id' => $id]);
        } elseif ($type === 'comment') {
            $this->db()->update('comments', ['status' => 'published'], 'id=:id', ['id' => $id]);
        }
        $this->json(['success' => true]);
    }

    public function reject(Request $request): void
    {
        $this->requireModerator();
        $this->verifyCsrf($request);
        $type = $request->post('type', '');
        $id   = (int)$request->post('id');
        $note = $request->post('note', '');
        if ($type === 'word') {
            $this->db()->update('words', ['status' => 'rejected'], 'id=:id', ['id' => $id]);
        } elseif ($type === 'contribution') {
            $this->db()->update('contributions', ['status' => 'rejected', 'review_note' => $note, 'reviewed_by' => \App\Core\Auth::id(), 'reviewed_at' => date('Y-m-d H:i:s')], 'id=:id', ['id' => $id]);
        } elseif ($type === 'comment') {
            $this->db()->update('comments', ['status' => 'hidden'], 'id=:id', ['id' => $id]);
        }
        $this->json(['success' => true]);
    }
}
