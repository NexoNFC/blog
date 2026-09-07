<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoCatalog;
use Illuminate\View\View;

class NfcPointController extends Controller
{
    public function index(): View
    {
        return view('admin.nfc-points.index', [
            'points' => DemoCatalog::nfcPoints(),
        ]);
    }
}
