<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UpdateScooterRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255|unique:scooters,name,'.$this->scooter->id,
            'max_speed' => 'sometimes|required|string|max:50',
            'range' => 'sometimes|required|string|max:50',
            'features' => 'sometimes|required|array',
            'features.*' => 'string|max:255',
            'quantity' => 'sometimes|required|integer|min:1',
            'description' => 'sometimes|required|array',
            'description.title' => 'required|string|max:255',
            'description.content' => 'required|string',
            'pricing' => 'sometimes|required|array',
            'pricing.1hour' => 'sometimes|required|numeric|min:0',
            'pricing.2hours' => 'sometimes|required|numeric|min:0',
            'pricing.4hours' => 'sometimes|required|numeric|min:0',
            'pricing.8hours' => 'sometimes|required|numeric|min:0',
            'pricing.daily' => 'sometimes|required|numeric|min:0',
            'pricing.weekly' => 'sometimes|required|numeric|min:0',
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
