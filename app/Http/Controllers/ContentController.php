<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\News;
use App\Services\VisitRecorder;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(private VisitRecorder $visits) {}

    public function show(string $slug): View|Response
    {
        $news = News::query()
            ->with(['category', 'importedContent'])
            ->where('slug', $slug)
            ->first();

        if ($news === null) {
            abort(404);
        }

        if ($news->status !== ContentStatus::Published) {
            return response()
                ->view('errors.unpublished', [
                    'title' => $news->title,
                ], 404);
        }

        $this->visits->recordNewsView($news);

        return view('contents.show', [
            'content' => $news->toPublicArray(),
        ]);
    }
}
