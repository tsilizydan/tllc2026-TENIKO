<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Proverb;

class ProverbController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $page = max(1, (int)$request->get('page', 1));
        $model = new Proverb();
        $paged = $model->paginate($page, 25, "1", [], 'created_at DESC');
        $this->render('admin/proverbs/index', ['paged' => $paged, 'pageTitle' => 'Manage Proverbs'], 'admin');
    }

    public function create(Request $request): void
    {
        $this->requireAdmin();
        $dialects = $this->db()->fetchAll("SELECT id, name FROM dialects ORDER BY name");
        $this->render('admin/proverbs/edit', ['proverb' => null, 'dialects' => $dialects, 'pageTitle' => 'Add Proverb'], 'admin');
    }

    public function store(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $text = trim($request->post('text', ''));
        $model = new Proverb();
        $model->create([
            'text'                 => $text,
            'slug'                 => slug($text) . '-' . time(),
            'translation_fr'       => $request->post('translation_fr', ''),
            'translation_en'       => $request->post('translation_en', ''),
            'meaning'              => $request->post('meaning', ''),
            'cultural_explanation' => $request->post('cultural_explanation', ''),
            'dialect_id'           => $request->post('dialect_id') ?: null,
            'status'               => $request->post('status', 'draft'),
            'created_by'           => \App\Core\Auth::id(),
        ]);
        $this->session->flash('success', 'Proverb added.');
        $this->redirect('/admin/proverbs');
    }

    public function edit(Request $request): void
    {
        $this->requireAdmin();
        $id = (int)$request->param('id');
        $proverb = (new Proverb())->find($id);
        if (!$proverb) { $this->abort(404); return; }
        $dialects = $this->db()->fetchAll("SELECT id, name FROM dialects ORDER BY name");
        $this->render('admin/proverbs/edit', ['proverb' => $proverb, 'dialects' => $dialects, 'pageTitle' => 'Edit Proverb'], 'admin');
    }

    public function update(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id    = (int)$request->param('id');
        $model = new Proverb();
        $model->update($id, [
            'text'                 => trim($request->post('text', '')),
            'translation_fr'       => $request->post('translation_fr', ''),
            'translation_en'       => $request->post('translation_en', ''),
            'meaning'              => $request->post('meaning', ''),
            'cultural_explanation' => $request->post('cultural_explanation', ''),
            'dialect_id'           => $request->post('dialect_id') ?: null,
            'status'               => $request->post('status', 'draft'),
        ]);
        $this->session->flash('success', 'Proverb updated.');
        $this->redirect('/admin/proverbs/' . $id . '/edit');
    }
}
