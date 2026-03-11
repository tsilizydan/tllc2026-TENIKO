<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;

class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAdmin();
        $db = $this->db();

        $stats = [
            'total_words'    => $db->count("SELECT COUNT(*) FROM words WHERE deleted_at IS NULL"),
            'total_proverbs' => $db->count("SELECT COUNT(*) FROM proverbs WHERE deleted_at IS NULL"),
            'total_users'    => $db->count("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND role != 'admin'"),
            'total_articles' => $db->count("SELECT COUNT(*) FROM articles WHERE deleted_at IS NULL"),
            'pending_words'  => $db->count("SELECT COUNT(*) FROM words WHERE status='pending' AND deleted_at IS NULL"),
            'pending_contrib'=> $db->count("SELECT COUNT(*) FROM contributions WHERE status='pending'"),
            'forum_topics'   => $db->count("SELECT COUNT(*) FROM topics WHERE deleted_at IS NULL"),
            'newsletter_subs'=> $db->count("SELECT COUNT(*) FROM newsletter_subscribers WHERE status='active'"),
        ];

        $recentWords = $db->fetchAll(
            "SELECT w.id, w.word, w.slug, w.status, w.created_at, u.display_name AS author
             FROM words w LEFT JOIN users u ON u.id=w.created_by
             WHERE w.deleted_at IS NULL ORDER BY w.created_at DESC LIMIT 8"
        );
        $recentUsers = $db->fetchAll(
            "SELECT id, username, email, role, status, created_at FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 8"
        );

        $this->render('admin/dashboard', [
            'stats'       => $stats,
            'recentWords' => $recentWords,
            'recentUsers' => $recentUsers,
            'pageTitle'   => 'Admin Dashboard — TENIKO',
        ], 'admin');
    }

    public function analytics(Request $request): void
    {
        $this->requireAdmin();
        $this->render('admin/analytics', ['pageTitle' => 'Analytics — TENIKO Admin'], 'admin');
    }

    public function analyticsData(Request $request): void
    {
        $this->requireAdmin();
        $db = $this->db();

        // Page views last 30 days
        $views = $db->fetchAll(
            "SELECT DATE(created_at) AS date, COUNT(*) AS count
             FROM analytics_logs WHERE event='page_view' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY DATE(created_at) ORDER BY date ASC"
        );
        // Searches last 30 days
        $searches = $db->fetchAll(
            "SELECT DATE(created_at) AS date, COUNT(*) AS count
             FROM analytics_logs WHERE event='search' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY DATE(created_at) ORDER BY date ASC"
        );
        // New users over time
        $newUsers = $db->fetchAll(
            "SELECT DATE(created_at) AS date, COUNT(*) AS count FROM users
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY date ASC"
        );
        // Most viewed words
        $topWords = $db->fetchAll("SELECT word, view_count FROM words WHERE status='published' ORDER BY view_count DESC LIMIT 10");

        $this->json(compact('views', 'searches', 'newUsers', 'topWords'));
    }
}
