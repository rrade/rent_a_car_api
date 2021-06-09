<?php

namespace App\Http\Requests\API;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'car_type' => 'required|exists:car_types,id',
            'start_date' =>  'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }
}
