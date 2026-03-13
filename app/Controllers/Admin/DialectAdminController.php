<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class DialectAdminController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $dialects = $this->db()->fetchAll("SELECT * FROM dialects ORDER BY name");
        $this->render('admin/dialects/index', [
            'dialects'  => $dialects,
            'pageTitle' => 'Dialects — TENIKO Admin',
        ], 'admin');
    }

    public function create(Request $request): void
    {
        $this->requireAdmin();
        $this->render('admin/dialects/edit', [
            'dialect'   => null,
            'pageTitle' => 'Add Dialect — TENIKO Admin',
        ], 'admin');
    }

    public function store(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $name = trim($request->post('name', ''));
        if (!$name) {
            $this->session->flash('error', 'Dialect name is required.');
            $this->redirect('/admin/dialects/create');
        }
        $this->db()->insert('dialects', [
            'name'        => $name,
            'code'        => strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name)),
            'region'      => trim($request->post('region', '')),
            'description' => trim($request->post('description', '')),
            'lat'         => $request->post('lat') ?: null,
            'lng'         => $request->post('lng') ?: null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        $this->session->flash('success', 'Dialect created.');
        $this->redirect('/admin/dialects');
    }

    public function edit(Request $request): void
    {
        $this->requireAdmin();
        $id = (int)$request->param('id');
        $dialect = $this->db()->fetch("SELECT * FROM dialects WHERE id = ?", [$id]);
        if (!$dialect) { $this->abort(404); return; }
        $this->render('admin/dialects/edit', [
            'dialect'   => $dialect,
            'pageTitle' => 'Edit Dialect: ' . $dialect['name'],
        ], 'admin');
    }

    public function update(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->update('dialects', [
            'name'        => trim($request->post('name', '')),
            'region'      => trim($request->post('region', '')),
            'description' => trim($request->post('description', '')),
            'lat'         => $request->post('lat') ?: null,
            'lng'         => $request->post('lng') ?: null,
            'updated_at'  => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $id]);
        $this->session->flash('success', 'Dialect updated.');
        $this->redirect('/admin/dialects');
    }

    public function destroy(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $id = (int)$request->param('id');
        $this->db()->delete('dialects', 'id = ?', [$id]);
        $this->session->flash('success', 'Dialect deleted.');
        $this->redirect('/admin/dialects');
    }
}
