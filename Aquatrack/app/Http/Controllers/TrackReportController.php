<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class TrackReportController extends Controller
{
    public function index()
    {
        return Inertia::render('TrackReport/TrackReport', [
            'title' => 'Track Report - Aquatrack',
            'description' => 'Track your water service reports and issues',
        ]);
    }
}
