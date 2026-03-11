<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Word;

class DictionaryController extends Controller
{
    public function index(Request $request): void
    {
        $wordModel = new Word();
        $page   = max(1, (int)$request->get('page', 1));
        $letter = $request->get('letter', '');

        if ($letter) {
            $results = $this->db()->fetchAll(
                "SELECT w.id, w.word, w.slug, w.part_of_speech,
                        (SELECT translation FROM translations WHERE word_id=w.id AND lang='fr' LIMIT 1) AS trans_fr
                 FROM words w WHERE w.status='published' AND w.deleted_at IS NULL AND w.word LIKE ?
                 ORDER BY w.word ASC LIMIT 50",
                [$letter . '%']
            );
        } else {
            $results = [];
        }

        $mostViewed = $this->cache->remember('most_viewed_words', 3600, fn() => $wordModel->mostViewed(10));

        $this->render('dictionary/index', [
            'results'    => $results,
            'letter'     => $letter,
            'mostViewed' => $mostViewed,
            'pageTitle'  => 'Dictionary — ' . ($letter ? "Words starting with {$letter}" : 'Browse Malagasy Words'),
            'metaDesc'   => 'Search the largest database of Malagasy words with definitions, translations, pronunciations and dialect variants.',
        ]);
    }

    public function search(Request $request): void
    {
        $query     = trim($request->get('q', ''));
        $wordModel = new Word();
        $results   = $query ? $wordModel->search($query, 30) : [];

        $this->render('dictionary/index', [
            'results'   => $results,
            'query'     => $query,
            'pageTitle' => $query ? "Search results for \"{$query}\"" : 'Dictionary Search',
            'metaDesc'  => "Search results for {$query} on TENIKO Malagasy Dictionary.",
        ]);
    }

    public function show(Request $request): void
    {
        $slug = $request->param('slug');
        $wordModel = new Word();
        $word = $wordModel->getFullEntry($slug);

        if (!$word) {
            $this->abort(404, 'Word not found.');
            return;
        }

        // Comments
        $comments = $this->db()->fetchAll(
            "SELECT c.*, u.username, u.display_name, u.avatar
             FROM comments c JOIN users u ON u.id = c.user_id
             WHERE c.entity_type='word' AND c.entity_id=? AND c.status='published' AND c.deleted_at IS NULL
             ORDER BY c.created_at ASC",
            [$word['id']]
        );

        // Reactions
        $reactions = $this->db()->fetchAll(
            "SELECT type, COUNT(*) as count FROM reactions
             WHERE entity_type='word' AND entity_id=? GROUP BY type", [$word['id']]
        );

        // SEO structured data
        $seoTitle = e($word['word']) . ' — Malagasy Dictionary | TENIKO';
        $seoDesc  = '';
        if (!empty($word['definitions'])) {
            $def = $word['definitions'][0]['text'] ?? '';
            $seoDesc = truncate($def, 150);
        }

        $this->render('dictionary/word', [
            'word'      => $word,
            'comments'  => $comments,
            'reactions' => $reactions,
            'pageTitle' => $seoTitle,
            'metaDesc'  => $seoDesc,
        ]);
    }
}
