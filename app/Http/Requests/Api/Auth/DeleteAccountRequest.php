<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pin' => 'required|string',
            'confirmation' => 'required|string|in:DELETE_MY_ACCOUNT',
        ];
    }

    public function messages(): array
    {
        return [
            'confirmation.in' => 'You must type "DELETE_MY_ACCOUNT" to confirm account deletion.',
        ];
    }

    /**
     * Validate that the provided PIN matches the authenticated participant's PIN.
     */
    public function validatePin(): bool
    {
        $participant = $this->user();

        return Hash::check($this->pin, $participant->pin);
    }

    /**
     * Get the body parameters for Scribe.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'pin' => [
                'description' => 'The participant\'s PIN for account verification.',
                'example' => '123456',
                'required' => true,
            ],
            'confirmation' => [
                'description' => 'Confirmation text. Must be exactly "DELETE_MY_ACCOUNT".',
                'example' => 'DELETE_MY_ACCOUNT',
                'required' => true,
            ],
        ];
    }
}
