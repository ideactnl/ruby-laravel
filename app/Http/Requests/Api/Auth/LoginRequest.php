<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_number' => 'required|string',
            'pin' => 'required|string',
        ];
    }

    public function attemptLogin(): ?User
    {
        $user = User::where('registration_number', $this->input('registration_number'))->first();
        if ($user && Hash::check($this->input('pin'), $user->pin)) {
            return $user;
        }
        return null;
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
                'description' => 'The user\'s registration number.',
                'example' => 'user123',
                'required' => true,
            ],
            'pin' => [
                'description' => 'The user\'s PIN.',
                'example' => '123456',
                'required' => true,
            ],
        ];
    }
}
