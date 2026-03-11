<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Auth;

class ContributeController extends Controller
{
    public function index(Request $request): void
    {
        $this->render('contribute/index', [
            'dialects'  => $this->db()->fetchAll("SELECT id, name FROM dialects ORDER BY name"),
            'pageTitle' => 'Contribute — Help Build TENIKO',
        ]);
    }

    public function submitWord(Request $request): void
    {
        $this->verifyCsrf($request);
        $this->db()->insert('contributions', [
            'type'       => 'word',
            'user_id'    => Auth::id(),
            'data'       => json_encode($request->post()),
            'notes'      => $request->post('notes', ''),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->session->flash('success', 'Thank you! Your word submission is under review.');
        $this->redirect('/contribute');
    }

    public function submitProverb(Request $request): void
    {
        $this->verifyCsrf($request);
        $this->db()->insert('contributions', [
            'type'       => 'proverb',
            'user_id'    => Auth::id(),
            'data'       => json_encode($request->post()),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->session->flash('success', 'Thank you! Your proverb submission is under review.');
        $this->redirect('/contribute');
    }

    public function submitCorrection(Request $request): void
    {
        $this->verifyCsrf($request);
        $this->db()->insert('contributions', [
            'type'       => 'correction',
            'user_id'    => Auth::id(),
            'data'       => json_encode($request->post()),
            'notes'      => $request->post('notes', ''),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->session->flash('success', 'Thank you! Your correction has been submitted for review.');
        $this->redirect('/contribute');
    }
}
