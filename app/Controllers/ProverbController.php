<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Proverb;

class ProverbController extends Controller
{
    public function index(Request $request): void
    {
        $model    = new Proverb();
        $page     = max(1, (int)$request->get('page', 1));
        $dialect  = (int)$request->get('dialect', 0);
        $paged    = $model->paginated($page, 20, $dialect ?: null);
        $dialects = $this->db()->fetchAll("SELECT id, name FROM dialects ORDER BY name");

        $this->render('proverbs/index', [
            'paged'     => $paged,
            'dialects'  => $dialects,
            'dialect'   => $dialect,
            'pageTitle' => 'Proverbs — Ohabolana Malagasy | TENIKO',
            'metaDesc'  => 'Discover Malagasy proverbs with translations, meanings, and cultural explanations.',
        ]);
    }

    public function show(Request $request): void
    {
        $id    = (int)$request->param('id');
        $model = new Proverb();
        $proverb = $model->getFullEntry($id);

        if (!$proverb) { $this->abort(404, 'Proverb not found.'); return; }

        $similar = $model->latest(4);
        $this->render('proverbs/show', [
            'proverb'   => $proverb,
            'similar'   => $similar,
            'pageTitle' => truncate($proverb['text'], 60) . ' — Malagasy Proverb | TENIKO',
            'metaDesc'  => $proverb['translation_fr'] ?? $proverb['meaning'] ?? '',
        ]);
    }
}
