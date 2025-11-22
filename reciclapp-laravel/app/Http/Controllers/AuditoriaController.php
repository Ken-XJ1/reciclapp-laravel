<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditoria; // Debes crear este modelo

class AuditoriaController extends Controller
{
    public function index()
    {
        $logs = Auditoria::orderBy('fecha', 'desc')
            ->limit(100)
            ->get();
        return view('admin.auditoria', compact('logs'));
    }
}
