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
        $settings = $model->allGrouped();
        $this->render('admin/settings', ['settings' => $settings, 'pageTitle' => 'Settings — TENIKO Admin'], 'admin');
    }

    public function update(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $model    = new SiteSetting();
        $incoming = $request->post('settings', []);
        foreach ($incoming as $key => $value) {
            $model->set($key, $value);
        }
        $this->cache->flush(); // Clear all cached settings
        $this->session->flash('success', 'Settings saved successfully.');
        $this->redirect('/admin/settings');
    }
}
