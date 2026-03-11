<?php
/** @var App\Core\Router $router */

// HOME
$router->get('/', 'HomeController', 'index', 'home');
$router->post('/newsletter/subscribe', 'HomeController', 'newsletter', 'newsletter.subscribe');

// DICTIONARY
$router->get('/dictionary', 'DictionaryController', 'index', 'dictionary');
$router->get('/search', 'DictionaryController', 'search', 'search');
$router->get('/word/{slug}', 'DictionaryController', 'show', 'word.show');

// PROVERBS
$router->get('/proverbs', 'ProverbController', 'index', 'proverbs');
$router->get('/proverb/{id}', 'ProverbController', 'show', 'proverb.show');

// CULTURE
$router->get('/culture', 'CultureController', 'index', 'culture');
$router->get('/article/{slug}', 'CultureController', 'article', 'article.show');

// DIALECTS
$router->get('/dialects', 'DialectController', 'index', 'dialects');
$router->get('/dialect/{slug}', 'DialectController', 'show', 'dialect.show');

// FORUMS
$router->get('/forums', 'ForumController', 'index', 'forums');
$router->get('/forum/{slug}', 'ForumController', 'show', 'forum.show');
$router->get('/topic/{id}', 'ForumController', 'topic', 'topic.show');
$router->post('/topic/create', 'ForumController', 'createTopic', 'topic.create');
$router->post('/post/reply', 'ForumController', 'reply', 'post.reply');

// CONTRIBUTE
$router->get('/contribute', 'ContributeController', 'index', 'contribute');
$router->post('/contribute/word', 'ContributeController', 'submitWord', 'contribute.word');
$router->post('/contribute/proverb', 'ContributeController', 'submitProverb', 'contribute.proverb');
$router->post('/contribute/correction', 'ContributeController', 'submitCorrection', 'contribute.correction');

// AUTH
$router->get('/login',    'AuthController', 'loginForm',  'login');
$router->post('/login',   'AuthController', 'login',      'login.post');
$router->get('/register', 'AuthController', 'registerForm','register');
$router->post('/register','AuthController', 'register',   'register.post');
$router->get('/logout',   'AuthController', 'logout',     'logout');
$router->get('/verify-email', 'AuthController', 'verifyEmail', 'verify.email');
$router->get('/forgot-password', 'AuthController', 'forgotForm', 'forgot.password');
$router->post('/forgot-password','AuthController', 'forgotPassword','forgot.password.post');
$router->get('/reset-password', 'AuthController', 'resetForm', 'reset.password');
$router->post('/reset-password','AuthController', 'resetPassword','reset.password.post');

// USER PROFILE
$router->get('/profile/{username}', 'UserController', 'profile', 'user.profile');
$router->get('/settings', 'UserController', 'settings', 'user.settings');
$router->post('/settings', 'UserController', 'updateSettings', 'user.settings.post');

// COMMENTS & REACTIONS (AJAX)
$router->post('/comment', 'CommentController', 'store', 'comment.store');
$router->post('/react', 'CommentController', 'react', 'react.store');

// API (JSON endpoints)
$router->get('/api/search', 'ApiController', 'search', 'api.search');
$router->get('/api/word-of-day', 'ApiController', 'wordOfDay', 'api.wod');
$router->get('/api/proverb-of-day', 'ApiController', 'proverbOfDay', 'api.pod');
$router->get('/api/notifications', 'ApiController', 'notifications', 'api.notifications');
$router->post('/api/notifications/read', 'ApiController', 'markRead', 'api.notifications.read');

// STATIC PAGES
$router->get('/about',   'PageController', 'about',   'about');
$router->get('/contact', 'PageController', 'contact', 'contact');
$router->post('/contact','PageController', 'sendContact','contact.post');

// SITEMAP
$router->get('/sitemap.xml', 'PageController', 'sitemap', 'sitemap');

// ----------------------------------------
// ADMIN ROUTES
// ----------------------------------------
$router->get('/admin',                    'Admin\DashboardController', 'index',       'admin.dashboard');
$router->get('/admin/analytics',          'Admin\DashboardController', 'analytics',   'admin.analytics');
$router->get('/admin/analytics/data',     'Admin\DashboardController', 'analyticsData','admin.analytics.data');

// Words
$router->get('/admin/words',              'Admin\WordController', 'index',    'admin.words');
$router->get('/admin/words/create',       'Admin\WordController', 'create',   'admin.words.create');
$router->post('/admin/words/create',      'Admin\WordController', 'store',    'admin.words.store');
$router->get('/admin/words/{id}/edit',    'Admin\WordController', 'edit',     'admin.words.edit');
$router->post('/admin/words/{id}/edit',   'Admin\WordController', 'update',   'admin.words.update');
$router->post('/admin/words/{id}/delete', 'Admin\WordController', 'destroy',  'admin.words.delete');

// Proverbs
$router->get('/admin/proverbs',           'Admin\ProverbController', 'index',  'admin.proverbs');
$router->get('/admin/proverbs/create',    'Admin\ProverbController', 'create', 'admin.proverbs.create');
$router->post('/admin/proverbs/create',   'Admin\ProverbController', 'store',  'admin.proverbs.store');
$router->get('/admin/proverbs/{id}/edit', 'Admin\ProverbController', 'edit',   'admin.proverbs.edit');
$router->post('/admin/proverbs/{id}/edit','Admin\ProverbController', 'update', 'admin.proverbs.update');

// Articles
$router->get('/admin/articles',           'Admin\ArticleController', 'index',  'admin.articles');
$router->get('/admin/articles/create',    'Admin\ArticleController', 'create', 'admin.articles.create');
$router->post('/admin/articles/create',   'Admin\ArticleController', 'store',  'admin.articles.store');
$router->get('/admin/articles/{id}/edit', 'Admin\ArticleController', 'edit',   'admin.articles.edit');
$router->post('/admin/articles/{id}/edit','Admin\ArticleController', 'update', 'admin.articles.update');

// Users
$router->get('/admin/users',              'Admin\UserController', 'index',      'admin.users');
$router->get('/admin/users/{id}',         'Admin\UserController', 'show',       'admin.users.show');
$router->post('/admin/users/{id}/role',   'Admin\UserController', 'updateRole', 'admin.users.role');
$router->post('/admin/users/{id}/status', 'Admin\UserController', 'updateStatus','admin.users.status');
$router->post('/admin/users/{id}/badge',  'Admin\UserController', 'awardBadge', 'admin.users.badge');

// Moderation
$router->get('/admin/moderation',         'Admin\ModerationController', 'index',          'admin.moderation');
$router->post('/admin/moderation/approve','Admin\ModerationController', 'approve',        'admin.moderation.approve');
$router->post('/admin/moderation/reject', 'Admin\ModerationController', 'reject',         'admin.moderation.reject');

// Settings
$router->get('/admin/settings',           'Admin\SettingsController', 'index',   'admin.settings');
$router->post('/admin/settings',          'Admin\SettingsController', 'update',  'admin.settings.post');

// Ads
$router->get('/admin/ads',                'Admin\AdController', 'index',  'admin.ads');
$router->post('/admin/ads/create',        'Admin\AdController', 'store',  'admin.ads.store');
$router->post('/admin/ads/{id}/edit',     'Admin\AdController', 'update', 'admin.ads.update');
$router->post('/admin/ads/{id}/delete',   'Admin\AdController', 'destroy','admin.ads.delete');

// Donations
$router->get('/admin/donations',          'Admin\DonationController', 'index',   'admin.donations');
$router->post('/admin/donations/campaign','Admin\DonationController', 'campaign','admin.donations.campaign');

// Newsletter
$router->get('/admin/newsletter',         'Admin\NewsletterController', 'index',  'admin.newsletter');
$router->get('/admin/newsletter/export',  'Admin\NewsletterController', 'export', 'admin.newsletter.export');
