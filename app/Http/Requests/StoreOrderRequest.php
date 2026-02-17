<?php

namespace App\Http\Requests;

use App\Models\PromoCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'city' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'houseNumber' => ['required', 'string', 'max:50'],
            'zip' => ['required', 'string', 'regex:/^\d{3}\s?\d{2}$/'],
            'promo_code' => ['nullable', 'string'],
        ];
    }

    /** @return array<int, \Closure> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $code = $this->input('promo_code');

                if (! $code) {
                    return;
                }

                $promoCode = PromoCode::query()->where('code', $code)->first();

                if (! $promoCode) {
                    $validator->errors()->add('promo_code', 'Promo kód neexistuje.');

                    return;
                }

                if (! $promoCode->isValid()) {
                    $validator->errors()->add('promo_code', 'Promo kód je neplatný alebo už bol použitý.');
                }

                $this->merge(['promo_code_model' => $promoCode]);
            },
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email je povinný.',
            'email.email' => 'Zadajte platný email.',
            'phone.regex' => 'Neplatný formát telefónu.',
            'city.required' => 'Mesto je povinné.',
            'city.max' => 'Mesto môže mať maximálne 255 znakov.',
            'street.required' => 'Ulica je povinná.',
            'street.max' => 'Ulica môže mať maximálne 255 znakov.',
            'houseNumber.required' => 'Číslo domu je povinné.',
            'houseNumber.max' => 'Číslo domu môže mať maximálne 50 znakov.',
            'zip.required' => 'PSČ je povinné.',
            'zip.regex' => 'PSČ musí byť vo formáte XXX XX.',
            'promo_code.string' => 'Promo kód musí byť text.',
        ];
    }

    public function getPromoCode(): ?PromoCode
    {
        /** @var ?PromoCode */
        return $this->input('promo_code_model');
    }
}
