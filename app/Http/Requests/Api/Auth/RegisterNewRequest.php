<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Participant;
use App\Models\ResearchSurveyParticipant;
use Illuminate\Support\Facades\Hash;

class RegisterNewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'registration_number' => 'required|string|unique:participants,registration_number',
            'pin' => 'required|string|min:6',
            'study_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!ResearchSurveyParticipant::where('study_number', $value)->exists()) {
                        $fail('The study number must exist in MGA survey participants.');
                    }
                },
            ],
            'dob' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (!ResearchSurveyParticipant::where('dob', $value)->exists()) {
                        $fail('The date of birth must exist in MGA survey participants.');
                    } else if (!ResearchSurveyParticipant::where('dob', $value)->where('study_number', request('study_number'))->exists()) {
                        $fail('The study number does not match the associated date of birth on record.');
                    }
                },
            ],
            'opt_in_for_research' => 'required|boolean',
        ];
    }

    /**
     * Register the participant.
     *
     * @return \App\Models\Participant
     */
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
            'study_number' => [
                'description' => 'The study number.',
                'example' => 'AB0000',
                'required' => true,
            ],
            'dob' => [
                'description' => 'The participant\'s date of birth (YYYY-MM-DD).',
                'example' => '1990-01-01',
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
