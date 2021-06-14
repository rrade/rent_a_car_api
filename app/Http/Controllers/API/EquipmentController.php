<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(){

        $res = Equipment::paginate(Vehicle::PER_PAGE);
        // dd('uso');
        return response()->json($res);

    }
}
