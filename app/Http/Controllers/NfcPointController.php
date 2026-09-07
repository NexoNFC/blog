<?php

namespace App\Http\Controllers;

use App\Support\DemoCatalog;
use Illuminate\View\View;

class NfcPointController extends Controller
{
    public function show(string $code): View
    {
        $point = DemoCatalog::nfcByCode($code);

        if ($point === null) {
            abort(404);
        }

        $contents = DemoCatalog::contentsBySlugs($point['content_slugs']);

        return view('nfc.show', [
            'point' => $point,
            'contents' => $contents,
        ]);
    }
}
