<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_number' => 'required|string',
        ];
    }

    /**
     * Get the body parameters for Scribe.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'registration_number' => [
                'description' => 'The registration number.',
                'example' => 'user123',
                'required' => true,
            ],
        ];
    }
}
