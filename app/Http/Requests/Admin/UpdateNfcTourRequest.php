<?php

namespace App\Http\Requests\Admin;

use App\Models\NfcPoint;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateNfcTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        $point = $this->route('nfcPoint');

        return $point instanceof NfcPoint
            && ($this->user()?->can('update', $point) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tour_enabled' => ['sometimes', 'boolean'],
            'tour_description' => ['nullable', 'string', 'max:2000'],
            'panorama' => ['nullable', 'image', 'max:12288'],
            'panorama_data' => ['nullable', 'string', 'max:20000000'],
            'nfc_marker_theta' => ['nullable', 'numeric', 'between:-6.3,6.3'],
            'nfc_marker_phi' => ['nullable', 'numeric', 'between:-1.6,1.6'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tour_enabled' => 'vista 360° pública',
            'tour_description' => 'descripción de la vista',
            'panorama' => 'imagen panorámica',
            'panorama_data' => 'imagen panorámica',
            'nfc_marker_theta' => 'posición horizontal del marcador',
            'nfc_marker_phi' => 'posición vertical del marcador',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'panorama.uploaded' => $this->panoramaUploadFailureMessage(),
            'panorama.image' => 'La imagen panorámica debe ser un archivo JPG, PNG o WEBP válido.',
            'panorama.max' => 'La imagen panorámica no puede superar los 12 MB.',
            'panorama_data.max' => 'La imagen panorámica no puede superar los 12 MB.',
            'tour_description.max' => 'La descripción no puede superar los 2000 caracteres.',
            'nfc_marker_theta.numeric' => 'Haz clic en el panorama para señalar la tarjeta NFC.',
            'nfc_marker_phi.numeric' => 'Haz clic en el panorama para señalar la tarjeta NFC.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $dataUrl = $this->input('panorama_data');

            if (! is_string($dataUrl) || $dataUrl === '') {
                return;
            }

            if (! preg_match('#^data:image/(jpeg|jpg|png|webp);base64,#i', $dataUrl)) {
                $validator->errors()->add(
                    'panorama_data',
                    'La imagen panorámica debe ser un archivo JPG, PNG o WEBP válido.',
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tour_enabled' => $this->boolean('tour_enabled'),
            'nfc_marker_theta' => $this->filled('nfc_marker_theta') ? $this->input('nfc_marker_theta') : null,
            'nfc_marker_phi' => $this->filled('nfc_marker_phi') ? $this->input('nfc_marker_phi') : null,
            'panorama_data' => $this->filled('panorama_data') ? $this->input('panorama_data') : null,
        ]);
    }

    private function panoramaUploadFailureMessage(): string
    {
        $file = $this->file('panorama');
        $error = $file?->getError();

        return match ($error) {
            \UPLOAD_ERR_INI_SIZE, \UPLOAD_ERR_FORM_SIZE => 'La imagen panorámica supera el tamaño máximo permitido (12 MB).',
            \UPLOAD_ERR_PARTIAL => 'La subida de la imagen se interrumpió. Vuelve a intentarlo.',
            \UPLOAD_ERR_NO_TMP_DIR, \UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo guardar el archivo temporal de la imagen. Reinicia el servidor local e inténtalo de nuevo.',
            \UPLOAD_ERR_EXTENSION => 'Una extensión de PHP bloqueó la subida de la imagen.',
            default => 'No se pudo subir la imagen panorámica. Vuelve a seleccionar el archivo e inténtalo de nuevo.',
        };
    }
}
