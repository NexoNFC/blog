<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'La gestión completa de categorías se habilitará en una tarea posterior.');
    }
}
