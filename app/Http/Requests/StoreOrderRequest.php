<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $minStartTime = now()->addMinutes(15)->format('Y-m-d H:i:s');

        return [
            'scooter_id' => 'required|exists:scooters,id',
            'status' => 'required|in:pending,completed,cancelled',
            'start_time' => 'required|date|after_or_equal:'.$minStartTime,
            'duration' => 'required|integer|in:1,2,4,8,24,168',
            'pickup_location' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'start_time.after_or_equal' => 'The start time must be at least 15 minutes from now.',
        ];
    }

    public function failedValidation(Validator $validator)
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
