<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoCatalog;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(): View
    {
        return view('admin.contents.index', [
            'contents' => DemoCatalog::contents(),
        ]);
    }

    public function create(): View
    {
        return view('admin.contents.create');
    }
}
