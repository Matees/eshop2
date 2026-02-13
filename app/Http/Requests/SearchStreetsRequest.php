<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchStreetsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:1'],
            'municipality' => ['required', 'string', 'min:1'],
        ];
    }
}
