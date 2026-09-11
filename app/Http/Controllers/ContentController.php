<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\VisitRecorder;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(private VisitRecorder $visits) {}

    public function show(string $slug): View
    {
        $news = News::query()
            ->published()
            ->with(['category', 'importedContent'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->visits->recordNewsView($news);

        return view('contents.show', [
            'content' => $news->toPublicArray(),
        ]);
    }
}
