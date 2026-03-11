<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Word;

class WordController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $page   = max(1, (int)$request->get('page', 1));
        $status = $request->get('status', '');
        $q      = $request->get('q', '');

        $where  = "deleted_at IS NULL";
        $params = [];
        if ($status) { $where .= " AND status=:status"; $params['status'] = $status; }
        if ($q)      { $where .= " AND word LIKE :q";   $params['q'] = "%{$q}%"; }

        $model = new Word();
        $paged = $model->paginate($page, 25, $where, $params);

        $this->render('admin/words/index', [
            'paged'     => $paged,
            'status'    => $status,
            'q'         => $q,
            'pageTitle' => 'Manage Words — TENIKO Admin',
        ], 'admin');
    }

    public function create(Request $request): void
    {
        $this->requireAdmin();
        $dialects = $this->db()->fetchAll("SELECT id, name FROM dialects ORDER BY name");
        $this->render('admin/words/edit', ['word' => null, 'dialects' => $dialects, 'pageTitle' => 'Add Word'], 'admin');
    }

    public function store(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);

        $model = new Word();
        $wordText = trim($request->post('word', ''));
        $slug  = slug($wordText);

        $id = $model->create([
            'word'           => $wordText,
            'slug'           => $slug . '-' . time(),
            'pronunciation'  => $request->post('pronunciation', ''),
            'part_of_speech' => $request->post('part_of_speech', 'noun'),
            'etymology'      => $request->post('etymology', ''),
            'status'         => $request->post('status', 'draft'),
            'created_by'     => \App\Core\Auth::id(),
        ]);

        // Save definitions
        $defs = $request->post('definitions', []);
        foreach ($defs as $lang => $text) {
            if (!trim($text)) continue;
            $this->db()->insert('definitions', [
                'word_id' => $id, 'lang' => $lang, 'text' => $text, 'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        // Save translations
        $trans = $request->post('translations', []);
        foreach ($trans as $lang => $text) {
            if (!trim($text)) continue;
            $this->db()->insert('translations', [
                'word_id' => $id, 'lang' => $lang, 'translation' => $text, 'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->session->flash('success', 'Word added successfully.');
        $this->redirect('/admin/words');
    }

    public function edit(Request $request): void
    {
        $this->requireAdmin();
        $id   = (int)$request->param('id');
        $word = $this->db()->fetch(
            "SELECT w.*,
                    (SELECT JSON_OBJECTAGG(lang, text) FROM definitions WHERE word_id=w.id) AS definitions_json,
                    (SELECT JSON_OBJECTAGG(lang, translation) FROM translations WHERE word_id=w.id) AS translations_json
             FROM words w WHERE w.id=? AND w.deleted_at IS NULL", [$id]
        );
        if (!$word) { $this->abort(404); return; }

        $dialects = $this->db()->fetchAll("SELECT id, name FROM dialects ORDER BY name");
        $this->render('admin/words/edit', ['word' => $word, 'dialects' => $dialects, 'pageTitle' => 'Edit Word'], 'admin');
    }

    public function update(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id    = (int)$request->param('id');
        $model = new Word();
        $model->update($id, [
            'word'           => trim($request->post('word', '')),
            'pronunciation'  => $request->post('pronunciation', ''),
            'part_of_speech' => $request->post('part_of_speech', ''),
            'etymology'      => $request->post('etymology', ''),
            'notes'          => $request->post('notes', ''),
            'status'         => $request->post('status', 'draft'),
        ]);

        // Replace definitions
        $this->db()->delete('definitions', 'word_id=?', [$id]);
        foreach ($request->post('definitions', []) as $lang => $text) {
            if (!trim($text)) continue;
            $this->db()->insert('definitions', ['word_id' => $id, 'lang' => $lang, 'text' => $text, 'created_at' => date('Y-m-d H:i:s')]);
        }
        // Replace translations
        $this->db()->delete('translations', 'word_id=?', [$id]);
        foreach ($request->post('translations', []) as $lang => $text) {
            if (!trim($text)) continue;
            $this->db()->insert('translations', ['word_id' => $id, 'lang' => $lang, 'translation' => $text, 'created_at' => date('Y-m-d H:i:s')]);
        }

        $this->session->flash('success', 'Word updated.');
        $this->redirect('/admin/words/' . $id . '/edit');
    }

    public function destroy(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        (new Word())->delete($id);
        $this->session->flash('success', 'Word deleted.');
        $this->redirect('/admin/words');
    }
}
