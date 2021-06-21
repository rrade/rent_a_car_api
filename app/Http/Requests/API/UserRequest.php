<?php

namespace App\Http\Requests\API;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UserRequest extends FormRequest
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
        $rules =  [
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'remarks' => 'nullable|string|max:1000',
        ];
        if($name == 'user-store'){
            $rules += ['email' => 'required|email|unique:clients,email|unique:users,email',
                'identification_document_no' =>  'required|alpha_num|unique:clients,identification_document_no',
                'phone_no'=> 'required|string|unique:clients,phone_no'];
        }
        return $rules;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }
}
