<?php

namespace App\Http\Requests\Admin;

use App\Models\News;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $news = $this->route('news');

        return $news instanceof News
            && ($this->user()?->can('update', $news) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'origin_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'summary.required' => 'El resumen es obligatorio.',
            'body.required' => 'El contenido es obligatorio.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'origin_url.url' => 'La URL original debe ser válida.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('category_id') === '') {
            $this->merge(['category_id' => null]);
        }

        if ($this->input('origin_url') === '') {
            $this->merge(['origin_url' => null]);
        }
    }
}
