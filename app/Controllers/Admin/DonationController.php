<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class DonationController extends Controller
{
    protected string $layout = 'admin';

    public function index(Request $request): void
    {
        $this->requireAdmin();
        $db = $this->db();

        // Summary stats
        $stats = [
            'total_raised'  => (float)($db->fetch("SELECT COALESCE(SUM(amount),0) AS v FROM donations WHERE status='completed'")['v'] ?? 0),
            'total_donors'  => (int)($db->fetch("SELECT COUNT(DISTINCT email) AS v FROM donations WHERE status='completed'")['v'] ?? 0),
            'this_month'    => (float)($db->fetch("SELECT COALESCE(SUM(amount),0) AS v FROM donations WHERE status='completed' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")['v'] ?? 0),
            'goal'          => (float)($db->fetch("SELECT value FROM site_settings WHERE `key`='donation_goal'")['value'] ?? 5000),
        ];

        $donations = $db->fetchAll("SELECT * FROM donations ORDER BY created_at DESC LIMIT 50");

        $campaign = [
            'goal'    => $stats['goal'],
            'message' => $db->fetch("SELECT value FROM site_settings WHERE `key`='donation_message'")['value'] ?? '',
        ];

        $this->render('admin/donations/index', [
            'stats'     => $stats,
            'donations' => $donations,
            'campaign'  => $campaign,
            'pageTitle' => 'Donations Management',
        ], 'admin');
    }

    public function campaign(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $db = $this->db();

        $settings = [
            'donation_goal'    => (float)$request->post('goal', 5000),
            'donation_message' => trim($request->post('message', '')),
        ];

        foreach ($settings as $key => $value) {
            $existing = $db->fetch("SELECT id FROM site_settings WHERE `key`=?", [$key]);
            if ($existing) {
                $db->update('site_settings', ['value' => $value], '`key` = :key', ['key' => $key]);
            } else {
                $db->insert('site_settings', ['key' => $key, 'value' => $value]);
            }
        }

        $this->session->flash('success', 'Donation campaign settings saved.');
        $this->redirect('/admin/donations');
    }
}
