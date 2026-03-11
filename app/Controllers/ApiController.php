<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Word;
use App\Models\Proverb;

class ApiController extends Controller
{
    public function search(Request $request): void
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) { $this->json([]); return; }

        $wordModel = new Word();
        $results   = $wordModel->autocomplete($q, 8);

        $data = array_map(fn($w) => [
            'id'             => $w['id'],
            'word'           => $w['word'],
            'slug'           => $w['slug'],
            'part_of_speech' => $w['part_of_speech'],
            'url'            => '/word/' . $w['slug'],
        ], $results);

        $this->json($data);
    }

    public function wordOfDay(Request $request): void
    {
        $wordModel = new Word();
        $word = $this->cache->remember('wod_api', 3600, fn() => $wordModel->wordOfDay());
        $this->json($word ?: []);
    }

    public function proverbOfDay(Request $request): void
    {
        $model = new Proverb();
        $p = $this->cache->remember('pod_api', 3600, fn() => $model->proverbOfDay());
        $this->json($p ?: []);
    }

    public function notifications(Request $request): void
    {
        if (!\App\Core\Auth::check()) { $this->json([]); return; }
        $userId = \App\Core\Auth::id();
        $notes  = $this->db()->fetchAll(
            "SELECT * FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 20",
            [$userId]
        );
        $this->json($notes);
    }

    public function markRead(Request $request): void
    {
        if (!\App\Core\Auth::check()) { $this->json(['ok' => false], 401); return; }
        $this->db()->update('notifications', ['is_read' => 1], 'user_id = :uid', ['uid' => \App\Core\Auth::id()]);
        $this->json(['ok' => true]);
    }
}
