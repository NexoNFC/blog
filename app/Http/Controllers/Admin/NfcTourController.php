<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateNfcTourRequest;
use App\Models\NfcPoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class NfcTourController extends Controller
{
    public function edit(NfcPoint $nfcPoint): View
    {
        $this->authorize('update', $nfcPoint);

        return view('admin.nfc-points.tour', [
            'point' => $nfcPoint,
            'tour' => [
                'panorama_url' => $nfcPoint->panoramaUrl(),
                'marker' => $nfcPoint->nfcMarkerArray(),
            ],
        ]);
    }

    public function update(UpdateNfcTourRequest $request, NfcPoint $nfcPoint): RedirectResponse
    {
        $this->authorize('update', $nfcPoint);

        $data = $request->validated();

        if (
            array_key_exists('nfc_marker_theta', $data)
            && array_key_exists('nfc_marker_phi', $data)
            && ($data['nfc_marker_theta'] === null || $data['nfc_marker_phi'] === null)
        ) {
            // “Quitar marcador” deja los hidden vacíos: al guardar se elimina.
            $data['nfc_marker_theta'] = null;
            $data['nfc_marker_phi'] = null;
        }

        $storedPanorama = null;

        if (filled($request->input('panorama_data'))) {
            try {
                $storedPanorama = $this->storePanoramaDataUrl((string) $request->input('panorama_data'));
            } catch (RuntimeException) {
                return back()
                    ->withInput()
                    ->withErrors(['panorama_data' => 'No se pudo procesar la imagen panorámica. Usa JPG, PNG o WEBP.']);
            }
        } elseif ($request->hasFile('panorama') && $request->file('panorama')?->isValid()) {
            $storedPanorama = $request->file('panorama')->store('tours/panoramas', 'public');
        }

        if (is_string($storedPanorama) && $storedPanorama !== '') {
            if (filled($nfcPoint->panorama_path)) {
                $this->deleteStoredPanorama($nfcPoint->panorama_path);
            }

            $data['panorama_path'] = $storedPanorama;
        }

        unset($data['panorama'], $data['panorama_data']);

        if (! array_key_exists('tour_enabled', $data)) {
            $data['tour_enabled'] = $request->boolean('tour_enabled');
        }

        if (($data['tour_enabled'] ?? false) && blank($data['panorama_path'] ?? $nfcPoint->panorama_path)) {
            return back()
                ->withInput()
                ->withErrors(['panorama' => 'Sube una imagen 360° antes de activar el recorrido.']);
        }

        $nfcPoint->update($data);

        return redirect()
            ->route('admin.nfc.tour.edit', $nfcPoint)
            ->with('status', 'La vista 360° del punto se actualizó correctamente.');
    }

    private function storePanoramaDataUrl(string $dataUrl): string
    {
        if (! preg_match('#^data:image/(jpeg|jpg|png|webp);base64,(.+)$#i', $dataUrl, $matches)) {
            throw new RuntimeException('Invalid panorama data URL.');
        }

        $extension = strtolower($matches[1]) === 'jpg' ? 'jpg' : strtolower($matches[1]);
        $binary = base64_decode($matches[2], true);

        if ($binary === false || $binary === '') {
            throw new RuntimeException('Invalid panorama payload.');
        }

        if (strlen($binary) > 12 * 1024 * 1024) {
            throw new RuntimeException('Panorama exceeds size limit.');
        }

        $path = 'tours/panoramas/'.Str::random(40).'.'.$extension;

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function deleteStoredPanorama(string $path): void
    {
        if (str_starts_with($path, 'images/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
