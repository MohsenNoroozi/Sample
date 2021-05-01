<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListDownloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'data.*' => ['required', 'numeric', 'min:0', 'max:10'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'data.required' => 'No List selected',
            'data.array' => 'Selected lists has not been received correctly.',
            'data.*.required' => 'Selected lists has not been received correctly.',
        ];
    }
}
