<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\Participant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_number' => 'required|string|unique:participants,registration_number',
            'pin' => 'required|string|min:6',
            'opt_in_for_research' => 'required|boolean',
        ];
    }

    public function registerParticipant(): Participant
    {
        $data = $this->validated();
        $data['pin'] = Hash::make($data['pin']);

        return Participant::create($data);
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
                'description' => 'The PIN code for mobile login (min 6 chars).',
                'example' => '123456',
                'required' => true,
            ],
            'opt_in_for_research' => [
                'description' => 'Whether the participant opts in for research. Accepts true/false or 1/0.',
                'example' => true,
                'required' => true,
            ],
        ];
    }
}
