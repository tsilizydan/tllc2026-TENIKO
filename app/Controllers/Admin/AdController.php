<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class AdController extends Controller
{
    protected string $layout = 'admin';

    public function index(Request $request): void
    {
        $this->requireAdmin();
        $db  = $this->db();
        $ads = $db->fetchAll("SELECT * FROM ads ORDER BY created_at DESC");

        $this->render('admin/ads/index', [
            'ads'       => $ads,
            'pageTitle' => 'Advertising Management',
        ], 'admin');
    }

    public function store(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);

        $name = trim($request->post('name', ''));
        if (empty($name)) {
            $this->session->flash('error', 'Ad name is required.');
            $this->redirect('/admin/ads');
        }

        $this->db()->insert('ads', [
            'name'       => $name,
            'placement'  => $request->post('placement', 'sidebar'),
            'type'       => $request->post('type', 'image'),
            'image_url'  => trim($request->post('image_url', '')),
            'link_url'   => trim($request->post('link_url', '')),
            'code'       => trim($request->post('code', '')),
            'start_date' => $request->post('start_date') ?: null,
            'end_date'   => $request->post('end_date') ?: null,
            'is_active'  => (int)(bool)$request->post('is_active'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->flash('success', 'Ad created successfully.');
        $this->redirect('/admin/ads');
    }

    public function update(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');

        $this->db()->update('ads', [
            'name'       => trim($request->post('name', '')),
            'placement'  => $request->post('placement', 'sidebar'),
            'type'       => $request->post('type', 'image'),
            'image_url'  => trim($request->post('image_url', '')),
            'link_url'   => trim($request->post('link_url', '')),
            'code'       => trim($request->post('code', '')),
            'start_date' => $request->post('start_date') ?: null,
            'end_date'   => $request->post('end_date') ?: null,
            'is_active'  => (int)(bool)$request->post('is_active'),
        ], 'id = :id', ['id' => $id]);

        $this->session->flash('success', 'Ad updated.');
        $this->redirect('/admin/ads');
    }

    public function destroy(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->delete('ads', 'id = :id', ['id' => $id]);
        $this->session->flash('success', 'Ad deleted.');
        $this->redirect('/admin/ads');
    }
}
