<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Dialect;

class DialectController extends Controller
{
    public function index(Request $request): void
    {
        try {
            $model    = new Dialect();
            $dialects = $model->all();
        } catch (\Throwable $e) {
            error_log('[TENIKO] DialectController::index error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->abort(500, 'Unable to load dialects.');
            return;
        }

        $this->render('dialects/index', [
            'dialects'  => $dialects,
            'pageTitle' => 'Malagasy Dialects — TENIKO',
            'metaDesc'  => 'Explore the regional dialects of Madagascar: vocabulary, pronunciation and cultural identity of each of the 18 Malagasy ethnic groups.',
        ]);
    }

    public function show(Request $request): void
    {
        $slug  = $request->param('slug');
        $model = new Dialect();

        try {
            $dialect = $model->findByCode($slug);
        } catch (\Throwable $e) {
            error_log('[TENIKO] DialectController::show findByCode error: ' . $e->getMessage());
            $this->abort(500, 'Unable to load dialect.');
            return;
        }

        if (!$dialect) {
            $this->abort(404, 'Dialect not found.');
            return;
        }

        try {
            $full = $model->getWithWords((int)$dialect['id']);
        } catch (\Throwable $e) {
            error_log('[TENIKO] DialectController::show getWithWords error: ' . $e->getMessage());
            // Fall back to just showing the dialect without word variants
            $full = $dialect;
            $full['word_variants'] = [];
        }

        $this->render('dialects/show', [
            'dialect'   => $full ?? $dialect,
            'pageTitle' => ($dialect['name'] ?? 'Dialect') . ' — Malagasy Dialect | TENIKO',
            'metaDesc'  => $dialect['description'] ?? 'Explore the ' . ($dialect['name'] ?? '') . ' dialect of the Malagasy language.',
        ]);
    }
}
