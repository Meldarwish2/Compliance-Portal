<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index()
    {
        $audits = Audit::with('user')->latest()->get();

        // // Optionally format the modified data
        // foreach ($audits as $audit) {
        //     $audit->modified = $audit->getModified();
        // }
        return view('audits.index', compact('audits'));
    }
}
