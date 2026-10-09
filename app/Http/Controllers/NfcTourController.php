<?php

namespace App\Http\Controllers;

use App\Models\NfcPoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NfcTourController extends Controller
{
    public function show(string $code): View|RedirectResponse
    {
        $point = NfcPoint::query()
            ->where('code', $code)
            ->firstOrFail();

        if (! $point->isActive() || ! $point->hasTour()) {
            return redirect()
                ->route('nfc.show', $point->code)
                ->with('status', 'Este punto aún no tiene vista 360° disponible.');
        }

        return view('nfc.tour', [
            'point' => $point->toTourArray(),
        ]);
    }
}
