<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Dialect;

class DialectController extends Controller
{
    private function writeLog(string $msg): void
    {
        $f = dirname(__DIR__, 2) . '/public/teniko-debug.txt';
        file_put_contents($f, date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND | LOCK_EX);
    }

    public function index(Request $request): void
    {
        $this->writeLog('DialectController::index() called — starting');
        try {
            $model    = new Dialect();
            $this->writeLog('Dialect model created — calling all()');
            $dialects = $model->all();
            $this->writeLog('Dialect all() returned ' . count($dialects) . ' rows');
        } catch (\Throwable $e) {
            $this->writeLog('[ERROR] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->writeLog('[TRACE] ' . $e->getTraceAsString());
            // Also die with plain text to bypass any buffering
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: text/plain; charset=UTF-8');
            die('[DIALECT INDEX ERROR] ' . get_class($e) . ': ' . $e->getMessage() . "\nFile: " . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString());
        }

        $this->writeLog('About to render dialects/index view');
        try {
            $this->render('dialects/index', [
                'dialects'  => $dialects,
                'pageTitle' => 'Dialect Map — Malagasy Dialects | TENIKO',
                'metaDesc'  => 'Explore the regional dialects of Madagascar with word variations and linguistic descriptions.',
            ]);
            $this->writeLog('render() completed successfully');
        } catch (\Throwable $e) {
            $this->writeLog('[RENDER ERROR] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->writeLog('[TRACE] ' . $e->getTraceAsString());
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: text/plain; charset=UTF-8');
            die('[DIALECT RENDER ERROR] ' . get_class($e) . ': ' . $e->getMessage() . "\nFile: " . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString());
        }
    }

    public function show(Request $request): void
    {
        $slug  = $request->param('slug');
        $model = new Dialect();

        try {
            $dialect = $model->findByCode($slug);
        } catch (\Throwable $e) {
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: text/plain; charset=UTF-8');
            die('[DIALECT SHOW findByCode ERROR] ' . get_class($e) . ': ' . $e->getMessage() . "\nFile: " . $e->getFile() . ':' . $e->getLine());
        }

        if (!$dialect) { $this->abort(404, 'Dialect not found.'); return; }

        try {
            $full = $model->getWithWords((int)$dialect['id']);
        } catch (\Throwable $e) {
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: text/plain; charset=UTF-8');
            die('[DIALECT SHOW getWithWords ERROR] ' . get_class($e) . ': ' . $e->getMessage() . "\nFile: " . $e->getFile() . ':' . $e->getLine());
        }

        try {
            $this->render('dialects/show', [
                'dialect'   => $full ?? $dialect,
                'pageTitle' => e($dialect['name'] ?? 'Dialect') . ' — TENIKO',
                'metaDesc'  => $dialect['description'] ?? "Explore the {$dialect['name']} dialect of Malagasy.",
            ]);
        } catch (\Throwable $e) {
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: text/plain; charset=UTF-8');
            die('[DIALECT SHOW RENDER ERROR] ' . get_class($e) . ': ' . $e->getMessage() . "\nFile: " . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString());
        }
    }
}
