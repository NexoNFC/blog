<?php

namespace App\Http\Controllers;

use App\Support\DemoCatalog;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function show(string $slug): View
    {
        $content = DemoCatalog::contentBySlug($slug);

        if ($content === null || $content['status'] !== 'publicado') {
            abort(404);
        }

        return view('contents.show', [
            'content' => $content,
        ]);
    }
}
