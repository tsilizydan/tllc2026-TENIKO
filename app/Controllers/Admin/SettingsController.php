<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\SiteSetting;

class SettingsController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $model    = new SiteSetting();
        // Use allFlat() — returns simple key→value array the view expects
        $settings = $model->allFlat();
        $this->render('admin/settings', [
            'settings'  => $settings,
            'pageTitle' => 'Settings — TENIKO Admin',
        ], 'admin');
    }

    public function update(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);

        $model    = new SiteSetting();
        $incoming = $request->post('settings', []);

        if (!is_array($incoming)) {
            $this->session->flash('error', 'Invalid settings data.');
            $this->redirect('/admin/settings');
        }

        foreach ($incoming as $key => $value) {
            // Sanitize key — only allow alphanumeric + underscore
            $key = preg_replace('/[^a-z0-9_]/', '', strtolower((string)$key));
            if ($key === '') continue;
            $model->set($key, trim((string)$value));
        }

        $this->session->flash('success', 'Settings saved successfully.');
        $this->redirect('/admin/settings');
    }
}
