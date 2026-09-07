<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function destroy(Request $request, string $news): RedirectResponse
    {
        return redirect()
            ->route('admin.news.index')
            ->with('status', "La eliminación de «{$news}» quedará disponible cuando exista el CRUD de noticias.");
    }
}
