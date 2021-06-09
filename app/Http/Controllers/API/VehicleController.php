<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\VehicleRequest;
use App\Models\Photo;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{

    public function index(Request $request)
    {

        $res = Vehicle::query()
        ->when($request->search, function($query) use ($request){
            $term = strtolower($request->search);
            //dd($term);
            $query->whereRaw("lower(plate_no) LIKE '%{$term}%' ")
                    ->orWhereRaw("lower(no_of_seats) LIKE '%{$term}%' ")
                ->orWhereRaw("lower(no_of_seats) LIKE '%{$term}%' ");
        });

        $res = $res->paginate(Vehicle::PER_PAGE);
        return response()->json($res);
    }


    public function store(VehicleRequest $request)
    {
       // dd($request->all());
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }
        // vehicle data
        $data = [
            'plate_no'=>$request->plate_no,
            'production_year'=>$request->production_year,
            'car_type_id'=>$request->car_type_id,
            'no_of_seats'=>$request->no_of_seats,
            'price_per_day'=>$request->price_per_day,
            'remarks'=>$request->remarks,
        ];
        $vehicle = Vehicle::query()->create($data);

        foreach ($request->validated($request->rules())['photos'] as $photo){
            $uploaded_path =  $photo->store('/public/vehicle-photos/'.$vehicle->id);
            $uploaded_path = str_replace('public/', 'storage/', $uploaded_path);
            $vehicle->photos()->create([
                'photo'=> $uploaded_path,
            ]);
        }

        return response()->json($vehicle->load('photos', 'carType'));
    }


    public function show($id)
    {
       $res =  Vehicle::query()->where('id',$id)->first();
        return response()->json($res);
    }


    public function update(VehicleRequest $request, $id)
    {
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }
        //dd($id);

        $validated = $request->validate([
            'plate_no' => "required|string|unique:vehicles,plate_no,$id",

        ]);

        // vehicle data
        $data = [
            'plate_no'=>$validated['plate_no'],
            'production_year'=>$request->production_year,
            'car_type_id'=>$request->car_type_id,
            'no_of_seats'=>$request->no_of_seats,
            'price_per_day'=>$request->price_per_day,
            'remarks'=>$request->remarks,
        ];

        $vehicle = Vehicle::query()->where('id','=',$id)->update($data);
        $vehicle = Vehicle::query()->where('id','=',$id)->first();

        Photo::query()->where('vehicle_id',$id)->delete();

        foreach ($request->validated($request->rules())['photos'] as $photo){
            $uploaded_path =  $photo->store('/public/vehicle-photos/'.$vehicle->id);
            $uploaded_path = str_replace('public/', 'storage/', $uploaded_path);
            $vehicle->photos()->create([
                'photo'=> $uploaded_path,
            ]);
        }

        return response()->json($vehicle->load('photos', 'carType'));


    }


    public function destroy($id)
    {
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }
        if (count(Reservation::query()->where('vehicle_id',$id)->get())>0){
            return response()->json(['message'=>'Can\'t delete vehicle witch has reservation attached to it!'],401);
        }
        Photo::query()->where('vehicle_id',$id)->delete();
        Vehicle::query()->where('id',$id)->delete();
        return response()->json(['message'=>'Success!'],200);

    }

}
