<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class PageController extends Controller
{
    public function about(Request $request): void
    {
        $this->render('pages/about', [
            'pageTitle' => 'About TENIKO — The Malagasy Language & Culture Encyclopedia',
            'metaDesc'  => 'Learn about TENIKO\'s mission to preserve and expand Malagasy linguistic and cultural knowledge.',
        ]);
    }

    public function contact(Request $request): void
    {
        $this->render('pages/contact', [
            'pageTitle' => 'Contact Us — TENIKO',
            'metaDesc'  => 'Get in touch with the TENIKO team.',
        ]);
    }

    public function sendContact(Request $request): void
    {
        $this->verifyCsrf($request);
        $name    = trim($request->post('name', ''));
        $email   = trim($request->post('email', ''));
        $subject = trim($request->post('subject', ''));
        $message = trim($request->post('message', ''));

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
            $this->session->flash('error', 'Please fill in all required fields with a valid email.');
            $this->redirect('/contact');
        }

        // Log to DB for admin review
        try {
            $this->db()->insert('activity_logs', [
                'event'      => 'contact_form',
                'data'       => json_encode(compact('name', 'email', 'subject', 'message')),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable) {}

        $this->session->flash('success', 'Thank you! Your message has been sent. We\'ll respond within 48 hours.');
        $this->redirect('/contact');
    }

    public function sitemap(Request $request): void
    {
        $db       = $this->db();
        $words    = $db->fetchAll("SELECT slug, updated_at FROM words WHERE status='published' AND deleted_at IS NULL ORDER BY updated_at DESC LIMIT 5000");
        $proverbs = $db->fetchAll("SELECT id, updated_at FROM proverbs WHERE status='published' AND deleted_at IS NULL");
        $articles = $db->fetchAll("SELECT slug, updated_at FROM articles WHERE status='published' AND deleted_at IS NULL");
        $dialects = $db->fetchAll("SELECT code AS slug FROM dialects");

        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $base = rtrim(env('APP_URL', 'https://teniko.tsilizy.com'), '/');

        $staticPages = ['/', '/dictionary', '/proverbs', '/culture', '/dialects', '/forums', '/contribute', '/about', '/contact'];
        foreach ($staticPages as $p) {
            echo "<url><loc>{$base}{$p}</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>";
        }
        foreach ($words as $w) {
            echo "<url><loc>{$base}/word/" . htmlspecialchars($w['slug']) . "</loc><lastmod>" . substr($w['updated_at'] ?? date('Y-m-d'), 0, 10) . "</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>";
        }
        foreach ($proverbs as $p) {
            echo "<url><loc>{$base}/proverb/{$p['id']}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>";
        }
        foreach ($articles as $a) {
            echo "<url><loc>{$base}/article/" . htmlspecialchars($a['slug']) . "</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>";
        }
        foreach ($dialects as $d) {
            echo "<url><loc>{$base}/dialect/" . htmlspecialchars($d['slug']) . "</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>";
        }

        echo '</urlset>';
        exit;
    }

    public function donate(Request $request): void
    {
        $db = $this->db();
        $goal    = (float)($db->fetch("SELECT value FROM site_settings WHERE `key`='donation_goal'")['value'] ?? 5000);
        $message = $db->fetch("SELECT value FROM site_settings WHERE `key`='donation_message'")['value'] ?? '';
        $raised  = (float)($db->fetch("SELECT COALESCE(SUM(amount),0) AS v FROM donations WHERE status='completed'")['v'] ?? 0);

        $this->render('pages/donate', [
            'goal'      => $goal,
            'raised'    => $raised,
            'message'   => $message,
            'progress'  => $goal > 0 ? min(100, round(($raised / $goal) * 100)) : 0,
            'pageTitle' => 'Support TENIKO — Donate',
            'metaDesc'  => 'Help us preserve Malagasy language and culture. Donate to TENIKO and support our digital archive mission.',
        ]);
    }

    public function processDonate(Request $request): void
    {
        $this->verifyCsrf($request);
        $amount = (float)$request->post('amount', 0);
        $name   = trim($request->post('name', 'Anonymous'));
        $email  = trim($request->post('email', ''));

        if ($amount <= 0) {
            $this->session->flash('error', 'Please enter a valid donation amount.');
            $this->redirect('/donate');
        }

        // Record donation intent (completed via Stripe webhook in production)
        try {
            $this->db()->insert('donations', [
                'donor_name'     => $name,
                'email'          => $email,
                'amount'         => $amount,
                'currency'       => 'EUR',
                'payment_method' => 'stripe',
                'status'         => 'pending',
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable) {}

        $this->session->flash('success', "Thank you {$name}! Your donation of €{$amount} is being processed.");
        $this->redirect('/donate');
    }
}
