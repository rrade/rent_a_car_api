<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarType;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CarTypeController extends Controller
{
    public function index(Request $request){

        $res = CarType::paginate(Vehicle::PER_PAGE);
        // dd('uso');
        return response()->json($res);

    }
}
