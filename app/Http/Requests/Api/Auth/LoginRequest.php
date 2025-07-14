<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\Participant;
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

    public function attemptLogin(): ?Participant
    {
        $participant = Participant::where('registration_number', $this->input('registration_number'))->first();
        if ($participant && Hash::check($this->input('pin'), $participant->pin)) {
            return $participant;
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
                'description' => 'The participant\'s registration number.',
                'example' => 'participant123',
                'required' => true,
            ],
            'pin' => [
                'description' => 'The participant\'s PIN.',
                'example' => '123456',
                'required' => true,
            ],
        ];
    }
}
