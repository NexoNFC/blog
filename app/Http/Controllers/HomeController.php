<?php

namespace App\Http\Controllers;

use App\Support\DemoCatalog;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $contents = DemoCatalog::publishedContents();
        $featured = $contents[0] ?? null;
        $rest = array_slice($contents, 1);

        $images = [];
        foreach ($contents as $content) {
            if (! empty($content['image'])) {
                $images[$content['slug']] = asset($content['image']);
            }
        }

        return view('home', [
            'featured' => $featured,
            'contents' => $rest,
            'newsImages' => $images,
            'steps' => DemoCatalog::landingSteps(),
            'locations' => DemoCatalog::campusLocations(),
        ]);
    }
}
