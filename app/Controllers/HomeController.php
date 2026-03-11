<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Word;
use App\Models\Proverb;
use App\Models\Article;
use App\Models\Announcement;

class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $wordModel    = new Word();
        $proverbModel = new Proverb();
        $articleModel = new Article();

        $wordOfDay    = $this->cache->remember('wod', 3600, fn() => $wordModel->wordOfDay());
        $proverbOfDay = $this->cache->remember('pod', 3600, fn() => $proverbModel->proverbOfDay());
        $featured     = $this->cache->remember('featured_articles', 1800, fn() => $articleModel->featured(4));
        $latestWords  = $this->cache->remember('latest_words', 900, fn() => $wordModel->latest(8));

        // Active announcements
        $announcements = $this->db()->fetchAll(
            "SELECT * FROM announcements WHERE is_active=1 AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY created_at DESC LIMIT 3"
        );

        // Active donation campaigns
        $campaign = $this->db()->fetch(
            "SELECT * FROM donation_campaigns WHERE status='active' ORDER BY created_at DESC LIMIT 1"
        );

        $this->render('home/index', [
            'wordOfDay'     => $wordOfDay,
            'proverbOfDay'  => $proverbOfDay,
            'featured'      => $featured,
            'latestWords'   => $latestWords,
            'announcements' => $announcements,
            'campaign'      => $campaign,
            'pageTitle'     => 'TENIKO — The Living Archive of Malagasy Language & Culture',
            'metaDesc'      => 'Discover Malagasy words, proverbs, culture and dialects. The largest collaborative digital archive of Malagasy language and cultural knowledge.',
        ]);
    }

    public function newsletter(Request $request): void
    {
        $email = trim($request->post('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->flash('error', 'Please enter a valid email address.');
            $this->redirect('/');
        }
        $exists = $this->db()->fetch("SELECT id FROM newsletter_subscribers WHERE email=?", [$email]);
        if (!$exists) {
            $this->db()->insert('newsletter_subscribers', [
                'email'      => $email,
                'status'     => 'active',
                'token'      => bin2hex(random_bytes(16)),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $this->session->flash('success', 'Thank you for subscribing to TENIKO newsletter!');
        } else {
            $this->session->flash('info', 'You are already subscribed.');
        }
        $this->redirect('/');
    }
}
