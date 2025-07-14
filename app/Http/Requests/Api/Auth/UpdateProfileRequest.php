<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enable_data_sharing' => 'nullable|boolean',
            'opt_in_for_research' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
            'pin' => 'required_with:password|string',
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
            'enable_data_sharing' => [
                'description' => 'Whether to enable data sharing for the participant.',
                'example' => false,
                'required' => false,
            ],
            'opt_in_for_research' => [
                'description' => 'Whether the participant opts in for research.',
                'example' => true,
                'required' => false,
            ],
            'password' => [
                'description' => 'New password for the participant (min 6 chars).',
                'example' => 'newpass123',
                'required' => false,
            ],
            'pin' => [
                'description' => 'PIN required when changing password.',
                'example' => '654321',
                'required' => false,
            ],
        ];
    }
}
