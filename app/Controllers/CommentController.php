<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Auth;

class CommentController extends Controller
{
    public function store(Request $request): void
    {
        $this->requireLogin();
        $this->verifyCsrf($request);

        $entityType = $request->post('entity_type', '');
        $entityId   = (int)$request->post('entity_id');
        $body       = trim($request->post('body', ''));

        if (!in_array($entityType, ['word', 'proverb', 'article']) || !$entityId || strlen($body) < 3) {
            $this->json(['error' => 'Invalid comment data.'], 400);
        }

        $id = $this->db()->insert('comments', [
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'user_id'     => Auth::id(),
            'body'        => $body,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        $user = Auth::user();
        $this->json(['success' => true, 'comment_id' => $id, 'username' => $user['display_name'] ?? $user['username']]);
    }

    public function react(Request $request): void
    {
        if (!Auth::check()) { $this->json(['error' => 'Login required.'], 401); }
        $this->verifyCsrf($request);

        $entityType = $request->post('entity_type', '');
        $entityId   = (int)$request->post('entity_id');
        $type       = $request->post('type', '');
        $validTypes = ['love','useful','popular','educational','interesting'];

        if (!in_array($entityType, ['word','proverb','article','comment']) || !$entityId || !in_array($type, $validTypes)) {
            $this->json(['error' => 'Invalid reaction.'], 400);
        }

        // Toggle: if exists, remove; if not, add
        $exists = $this->db()->fetch(
            "SELECT id FROM reactions WHERE entity_type=? AND entity_id=? AND user_id=?",
            [$entityType, $entityId, Auth::id()]
        );
        if ($exists) {
            $this->db()->delete('reactions', 'id=?', [$exists['id']]);
        } else {
            $this->db()->insert('reactions', [
                'entity_type' => $entityType, 'entity_id' => $entityId,
                'user_id'     => Auth::id(), 'type' => $type,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }
        $count = $this->db()->count(
            "SELECT COUNT(*) FROM reactions WHERE entity_type=? AND entity_id=?", [$entityType, $entityId]
        );
        $this->json(['success' => true, 'toggled' => !$exists, 'total' => $count]);
    }
}
