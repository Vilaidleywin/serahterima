<?php

namespace App\Http\Controllers;

use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $total   = Document::count();
        $pending = Document::where('status', 'PENDING')->count();
        $done    = Document::where('status', 'DONE')->count();

        $latest  = Document::orderByDesc('date')->take(10)->get();

        return view('dashboard', [
            'title' => 'Dashboard',
            'breadcrumb' => [
                ['label' => 'Dashboard'],
            ],
            'total' => $total,
            'pending' => $pending,
            'done' => $done,
            'latest' => $latest,
        ]);
    }
}
