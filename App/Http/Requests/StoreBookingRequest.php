<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // for production env, implement proper authorization logic
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guest_name' => 'required|string|max:255',
            'room_number' => 'required|string|max:50',
            'check_in_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'check_out_date' => [
                'required',
                'date',
                'after:check_in_date',
            ],
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'check_in_date.after_or_equal' => 'Check-in date must be today or later.',
            'check_out_date.after' => 'Check-out date must be after the check-in date.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert dates to consistent format
        if ($this->has('check_in_date')) {
            $this->merge([
                'check_in_date' => Carbon::parse($this->check_in_date)->toDateString(),
            ]);
        }

        if ($this->has('check_out_date')) {
            $this->merge([
                'check_out_date' => Carbon::parse($this->check_out_date)->toDateString(),
            ]);
        }
    }
}