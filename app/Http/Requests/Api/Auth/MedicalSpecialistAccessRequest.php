<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class MedicalSpecialistAccessRequest extends FormRequest
{
    /**
     * The participant instance.
     *
     * @var \App\Models\Participant
     */
    protected $participant;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->participant = $this->user();

        return $this->participant !== null;
    }

    /**
     * Get the expiry time for the PIN.
     *
     * @return \Carbon\Carbon|null
     */
    public function getExpiryTime()
    {
        if ($this->action !== 'enable') {
            return null;
        }

        return now()->addDays(7);
    }

    /**
     * Get the response message.
     *
     * @return string
     */
    public function getMessage()
    {
        if ($this->action !== 'enable') {
            return 'Medical specialist access disabled';
        }

        $expiry = $this->getExpiryTime();

        return "PIN created successfully (Valid until: {$expiry->format('M j, Y H:i')})";
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:enable,disable'],
            'pin' => [
                'required_if:action,enable',
                'string',
                'min:4',
                'max:6',
                'regex:/^\d+$/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'The action field is required.',
            'action.in' => 'The selected action is invalid. Must be either "enable" or "disable".',
            'pin.required_if' => 'The pin field is required when action is enable.',
            'pin.min' => 'The pin must be at least 4 digits.',
            'pin.max' => 'The pin must not be greater than 6 digits.',
            'pin.regex' => 'The pin must contain only numbers.',
        ];
    }

    /**
     * Get the body parameters for Scribe API documentation.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'action' => [
                'description' => 'Action to perform: "enable" to set a new PIN (valid for 7 days), or "disable" to remove PIN and disable specialist access.',
                'example' => 'enable',
            ],
            'pin' => [
                'description' => 'The numeric PIN to set for medical specialist access (required only when action is "enable"). Must be 4-6 digits.',
                'example' => '1234',
            ],
        ];
    }
}
