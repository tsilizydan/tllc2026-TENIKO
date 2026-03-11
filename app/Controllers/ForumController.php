<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Auth;

class ForumController extends Controller
{
    public function index(Request $request): void
    {
        $forums = $this->db()->fetchAll(
            "SELECT f.*, 
                    (SELECT t.title FROM topics t WHERE t.forum_id=f.id AND t.deleted_at IS NULL ORDER BY t.last_post_at DESC LIMIT 1) AS last_topic
             FROM forums f ORDER BY f.sort_order"
        );
        $this->render('forums/index', [
            'forums'    => $forums,
            'pageTitle' => 'Forums — TENIKO Community',
            'metaDesc'  => 'Join the TENIKO community to discuss Malagasy language, culture, dialects, and proverbs.',
        ]);
    }

    public function show(Request $request): void
    {
        $slug  = $request->param('slug');
        $forum = $this->db()->fetch("SELECT * FROM forums WHERE slug=?", [$slug]);
        if (!$forum) { $this->abort(404, 'Forum not found.'); return; }

        $page   = max(1, (int)$request->get('page', 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;
        $total  = $this->db()->count("SELECT COUNT(*) FROM topics WHERE forum_id=? AND deleted_at IS NULL", [$forum['id']]);
        $topics = $this->db()->fetchAll(
            "SELECT t.*, u.username, u.display_name, u.avatar FROM topics t
             JOIN users u ON u.id = t.user_id
             WHERE t.forum_id=? AND t.deleted_at IS NULL
             ORDER BY t.status='pinned' DESC, t.last_post_at DESC
             LIMIT ? OFFSET ?",
            [$forum['id'], $limit, $offset]
        );
        $this->render('forums/show', [
            'forum'     => $forum,
            'topics'    => $topics,
            'total'     => $total,
            'page'      => $page,
            'perPage'   => $limit,
            'pageTitle' => e($forum['name']) . ' — TENIKO Forums',
        ]);
    }

    public function topic(Request $request): void
    {
        $id    = (int)$request->param('id');
        $topic = $this->db()->fetch(
            "SELECT t.*, u.username, u.display_name, u.avatar, f.name AS forum_name, f.slug AS forum_slug
             FROM topics t JOIN users u ON u.id=t.user_id JOIN forums f ON f.id=t.forum_id
             WHERE t.id=? AND t.deleted_at IS NULL", [$id]
        );
        if (!$topic) { $this->abort(404, 'Topic not found.'); return; }

        // Increment view count
        $this->db()->query("UPDATE topics SET view_count=view_count+1 WHERE id=?", [$id]);

        $page   = max(1, (int)$request->get('page', 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;
        $posts  = $this->db()->fetchAll(
            "SELECT p.*, u.username, u.display_name, u.avatar, u.role, u.reputation
             FROM posts p JOIN users u ON u.id=p.user_id
             WHERE p.topic_id=? AND p.status='published' AND p.deleted_at IS NULL
             ORDER BY p.created_at ASC LIMIT ? OFFSET ?",
            [$id, $limit, $offset]
        );
        $total = $this->db()->count("SELECT COUNT(*) FROM posts WHERE topic_id=? AND status='published' AND deleted_at IS NULL", [$id]);

        $this->render('forums/topic', [
            'topic'     => $topic,
            'posts'     => $posts,
            'total'     => $total,
            'page'      => $page,
            'perPage'   => $limit,
            'pageTitle' => e($topic['title']) . ' — TENIKO Forums',
        ]);
    }

    public function createTopic(Request $request): void
    {
        $this->requireLogin();
        $this->verifyCsrf($request);

        $forumId = (int)$request->post('forum_id');
        $title   = trim($request->post('title', ''));
        $body    = trim($request->post('body', ''));

        if (!$forumId || !$title || !$body) {
            $this->session->flash('error', 'Please fill in all fields.');
            $this->redirect('/forums');
        }

        $slug = slug($title) . '-' . time();
        $topicId = $this->db()->insert('topics', [
            'forum_id'   => $forumId,
            'user_id'    => Auth::id(),
            'title'      => $title,
            'slug'       => $slug,
            'body'       => $body,
            'last_post_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->db()->query("UPDATE forums SET topic_count=topic_count+1 WHERE id=?", [$forumId]);
        $this->redirect('/topic/' . $topicId);
    }

    public function reply(Request $request): void
    {
        $this->requireLogin();
        $this->verifyCsrf($request);

        $topicId = (int)$request->post('topic_id');
        $body    = trim($request->post('body', ''));
        if (!$topicId || !$body) { $this->redirect('/forums'); }

        $topic = $this->db()->fetch("SELECT * FROM topics WHERE id=?", [$topicId]);
        if (!$topic || $topic['status'] === 'closed') {
            $this->session->flash('error', 'This topic is closed or does not exist.');
            $this->redirect('/forums');
        }

        $this->db()->insert('posts', [
            'topic_id'   => $topicId,
            'user_id'    => Auth::id(),
            'body'       => $body,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->db()->query(
            "UPDATE topics SET reply_count=reply_count+1, last_post_at=NOW() WHERE id=?", [$topicId]
        );
        $this->db()->query(
            "UPDATE forums SET post_count=post_count+1 WHERE id=?", [$topic['forum_id']]
        );
        $this->redirect('/topic/' . $topicId . '#bottom');
    }
}
