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
        $model   = new Dialect();
        $dialects = $model->all();
        $this->render('dialects/index', [
            'dialects'  => $dialects,
            'pageTitle' => 'Dialect Map — Malagasy Dialects | TENIKO',
            'metaDesc'  => 'Explore the regional dialects of Madagascar with word variations and linguistic descriptions.',
        ]);
    }

    public function show(Request $request): void
    {
        $slug  = $request->param('slug');
        $model = new Dialect();

        // Find dialect by code (slug)
        $dialect = $model->findByCode($slug);
        if (!$dialect) { $this->abort(404, 'Dialect not found.'); return; }

        $full = $model->getWithWords((int)$dialect['id']);  // PDO returns strings; cast to int
        $this->render('dialects/show', [
            'dialect'   => $full ?? $dialect,
            'pageTitle' => e($dialect['name'] ?? 'Dialect') . ' — TENIKO',
            'metaDesc'  => $dialect['description'] ?? "Explore the {$dialect['name']} dialect of Malagasy.",
        ]);
    }
}
