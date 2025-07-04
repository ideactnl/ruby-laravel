<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_number' => 'required|string|unique:users,registration_number',
            'pin' => 'required|string|min:6',
            'opt_in_for_research' => 'required|boolean',
        ];
    }

    public function registerUser(): User
    {
        $data = $this->validated();
        $data['pin_hash'] = Hash::make($data['pin']);
        unset($data['pin']);
        return User::create($data);
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
                'description' => 'The unique registration number for the user.',
                'example' => 'user123',
                'required' => true,
            ],
            'pin' => [
                'description' => 'The PIN code for mobile login (min 6 chars).',
                'example' => '123456',
                'required' => true,
            ],
            'opt_in_for_research' => [
                'description' => 'Must be true to register.',
                'example' => true,
                'required' => true,
            ],
        ];
    }
}
