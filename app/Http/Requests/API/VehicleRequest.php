<?php

namespace App\Http\Requests\API;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class VehicleRequest extends FormRequest
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
        $name = Route::currentRouteName();
        //$id = $request->route('id');;
        $rules =  [
            'production_year' => 'required|string|max:255',
            'car_type_id' => 'required|exists:car_types,id',
            'no_of_seats' => 'required|numeric',
            'price_per_day' => 'required',
            'remarks' => 'nullable|string',
            'photo' => 'nullable',
            'photo.*' => 'mimes:jpeg,png,jpg,gif,svg',
        ];
        if($name == 'vehicle-store'){
            $rules += ['plate_no' => 'required|string|unique:vehicles,plate_no',];
        }
//        if($name == 'vehicle-update'){
//            $rules += ['plate_no' => "required|string|unique:vehicles,plate_no,$id",];
//        }

        return $rules;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    public function  validated()
    {
        $data = parent::validated();
        foreach ($this->photo as $key => $p){
            $data['photos'][] = $p;
        }
        return $data;
    }
}
