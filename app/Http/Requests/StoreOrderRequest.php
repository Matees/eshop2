<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'regex:/^(\+421[\s\-]?9[0-9]{2}|\+420[\s\-]?[0-9]{3}|09[0-9]{2})[\s\-]?[0-9]{3}[\s\-]?[0-9]{3}$/'],
        ];
    }
}
