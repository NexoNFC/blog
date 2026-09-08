<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContentStatus;
use App\Models\NfcPoint;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNfcPointAssociationRequest extends FormRequest
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
            'news_id' => [
                'nullable',
                'integer',
                Rule::exists('news', 'id')->where('status', ContentStatus::Published->value),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'news_id.exists' => 'La noticia seleccionada no es válida.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('news_id') === '') {
            $this->merge(['news_id' => null]);
        }
    }
}
