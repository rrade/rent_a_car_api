<?php

namespace App\Http\Requests\API;

use App\Exceptions\ValidationException;
use App\Rules\EquipmentQuantity;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            'vehicle_id' => 'required|exists:vehicles,id',
            'client_id' => 'required|exists:clients,id',
            'from_date' =>  'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'rent_location_id' => 'required|exists:locations,id',
            'return_location_id' => 'required|exists:locations,id',
            'equipment'=>'array',
            'equipment.*.equipment_id' => 'int|exists:equipment,id',
            'equipment.*.quantity' => ['int'],
            'equipment.*' => new EquipmentQuantity()
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }
}
