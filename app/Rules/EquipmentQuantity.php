<?php

namespace App\Rules;

use App\Models\Equipment;
use Illuminate\Contracts\Validation\Rule;

class EquipmentQuantity implements Rule
{
private $max = '';
private  $equipment_name = '';
private  $value = '';

    public function passes($attribute, $value)
    {
        //dd($value);
        $equipment = Equipment::query()->where('id','=', $value['equipment_id'])->first();
        //dd($equipment);
        $this->max = $equipment->max_quantity;
        $this->equipment_name = $equipment->name;
        $this->value = $value['quantity'];

        if ($equipment && ($equipment->max_quantity >= $value['quantity'])){
            return true;
        }
        return false;
    }


    public function message()
    {
        return 'Max quantity for equipment '.$this->equipment_name.' is '.$this->max.', your value is '.$this->value.'.';
    }
}
