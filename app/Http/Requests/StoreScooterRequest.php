<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class StoreScooterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:scooters,name',
            'max_speed' => 'required|string|max:50',
            'range' => 'required|string|max:50',
            'features' => 'required|array',
            'features.*' => 'string|max:255',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|array',
            'description.title' => 'required|string|max:255',
            'description.content' => 'required|string',
            'pricing' => 'required|array',
            'pricing.1' => 'required|numeric|min:0',
            'pricing.2' => 'required|numeric|min:0',
            'pricing.4' => 'required|numeric|min:0',
            'pricing.8' => 'required|numeric|min:0',
            'pricing.24' => 'required|numeric|min:0',
            'pricing.168' => 'required|numeric|min:0',
        ];
    }

    public function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException(
            $this->validator,
            response()->json([
                'message' => 'Validation failed.',
                'errors' => $this->validator->errors(),
            ], 422)
        );
    }
}
