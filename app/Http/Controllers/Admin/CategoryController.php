<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Category::class);

        return view('admin.categories.index', [
            'categories' => Category::query()
                ->withCount('news')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $name = $request->validated('name');

        Category::query()->create([
            'name' => $name,
            'slug' => Category::uniqueSlugFrom($name),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'La categoría se registró correctamente.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $category->update([
            'name' => $request->validated('name'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'La categoría se actualizó correctamente.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        if ($category->news()->exists()) {
            return back()->with('alert', [
                'type' => 'danger',
                'title' => 'No se puede completar la operación',
                'message' => 'Esta categoría está asignada a noticias. Reasígnalas antes de eliminarla.',
            ]);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'La categoría se eliminó correctamente.');
    }
}
