<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $page  = max(1, (int)$request->get('page', 1));
        $model = new Article();
        $paged = $model->paginate($page, 20, "1", [], 'created_at DESC');
        $this->render('admin/articles/index', ['paged' => $paged, 'pageTitle' => 'Manage Articles'], 'admin');
    }

    public function create(Request $request): void
    {
        $this->requireAdmin();
        $categories = $this->db()->fetchAll("SELECT id, name FROM categories ORDER BY sort_order");
        $this->render('admin/articles/edit', ['article' => null, 'categories' => $categories, 'pageTitle' => 'New Article'], 'admin');
    }

    public function store(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $title  = trim($request->post('title', ''));
        $model  = new Article();
        $id     = $model->create([
            'title'        => $title,
            'slug'         => slug($title) . '-' . time(),
            'excerpt'      => $request->post('excerpt', ''),
            'body'         => $request->post('body', ''),
            'category_id'  => $request->post('category_id') ?: null,
            'type'         => $request->post('type', 'article'),
            'status'       => $request->post('status', 'draft'),
            'featured'     => $request->post('featured', 0) ? 1 : 0,
            'author_id'    => \App\Core\Auth::id(),
            'published_at' => $request->post('status') === 'published' ? date('Y-m-d H:i:s') : null,
        ]);
        $this->session->flash('success', 'Article created.');
        $this->redirect('/admin/articles/' . $id . '/edit');
    }

    public function edit(Request $request): void
    {
        $this->requireAdmin();
        $id      = (int)$request->param('id');
        $article = (new Article())->find($id);
        if (!$article) { $this->abort(404); return; }
        $categories = $this->db()->fetchAll("SELECT id, name FROM categories ORDER BY sort_order");
        $this->render('admin/articles/edit', ['article' => $article, 'categories' => $categories, 'pageTitle' => 'Edit Article'], 'admin');
    }

    public function update(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id    = (int)$request->param('id');
        $model = new Article();
        $model->update($id, [
            'title'        => trim($request->post('title', '')),
            'excerpt'      => $request->post('excerpt', ''),
            'body'         => $request->post('body', ''),
            'category_id'  => $request->post('category_id') ?: null,
            'type'         => $request->post('type', 'article'),
            'status'       => $request->post('status', 'draft'),
            'featured'     => $request->post('featured', 0) ? 1 : 0,
        ]);
        $this->session->flash('success', 'Article updated.');
        $this->redirect('/admin/articles/' . $id . '/edit');
    }
}
