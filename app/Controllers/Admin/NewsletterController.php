<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class NewsletterController extends Controller
{
    protected string $layout = 'admin';
    protected string $guard  = 'admin';

    public function index(Request $request): void
    {
        $db   = $this->db();
        $page = max(1, (int)$request->get('page', 1));
        $per  = 50;
        $offset = ($page - 1) * $per;

        $total = (int)($db->fetch("SELECT COUNT(*) AS c FROM newsletter_subscribers")['c'] ?? 0);
        $subs  = $db->fetchAll(
            "SELECT * FROM newsletter_subscribers ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$per, $offset]
        ) ?? [];

        $stats = [
            'total'     => $total,
            'confirmed' => (int)($db->fetch("SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE confirmed=1")['c'] ?? 0),
            'this_month'=> (int)($db->fetch("SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")['c'] ?? 0),
        ];

        $this->render('admin/newsletter/index', [
            'subs'      => $subs,
            'stats'     => $stats,
            'paged'     => [
                'items'        => $subs,
                'total'        => $total,
                'current_page' => $page,
                'last_page'    => max(1, (int)ceil($total / $per)),
            ],
            'pageTitle' => 'Newsletter Management',
        ], 'admin');
    }

    public function export(Request $request): void
    {
        $this->requireAdmin();
        $subs = $this->db()->fetchAll(
            "SELECT email, created_at FROM newsletter_subscribers WHERE confirmed=1 ORDER BY email"
        ) ?? [];

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="newsletter-subscribers-' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Email', 'Subscribed']);
        foreach ($subs as $s) {
            fputcsv($out, [$s['email'], $s['created_at']]);
        }
        fclose($out);
        exit;
    }

    public function remove(Request $request): void
    {
        $this->requireAdmin();
        $this->verifyCsrf($request);
        $email = trim($request->post('email', ''));
        if ($email) {
            $this->db()->delete('newsletter_subscribers', 'email = :email', ['email' => $email]);
            $this->session->flash('success', 'Subscriber removed.');
        }
        $this->redirect('/admin/newsletter');
    }
}
