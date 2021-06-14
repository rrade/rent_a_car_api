<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\AvailabilityRequest;
use App\Http\Requests\API\ReservationRequest;
use App\Models\EquipmentReservation;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() :JsonResponse
    {
        //mozes ovako da vracas nestovani objekat ili u modelu da zadas with i das ime relacije
        $res = Reservation::query()->with([
            'rentLocation' => function($query){
                $query->select(['id','name']);
            },
            'returnLocation' => function($query){
                $query->select(['id','name']);
            },
            'vehicle' => function($query){
                $query->with('carType');
            },
            'equipment',
            'client'
        ]);
        if (auth()->user()->role_id == Role::USER){

            $res = $res->where('client_id','=', auth()->user()->client_id);
        }
        $res = $res->paginate(Reservation::PER_PAGE);
        return response()->json($res);
    }
    public function avaliable(AvailabilityRequest $request)
    {
        $avaliable = Vehicle::query()
            ->where('car_type_id','=',$request->car_type)
            ->available($request->start_date,$request->end_date)
            ->paginate(Reservation::PER_PAGE);
        return response()->json($avaliable);
    }


    public function store(ReservationRequest $request)
    {
        //dd($request->except('equipment'));
       // $reservation = Reservation::query()->create($request->validated());
        // make the date format right for using Carbon

       abort_unless(Vehicle::query()->find($request->vehicle_id)->isAvailable($request->from_date,$request->to_date),422,'Vehicle is unavaliable');


        $request->to_date .= ' 00:00:00';
        $request->from_date .= ' 00:00:00';

        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $request->to_date);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $request->from_date);
        $diff_in_days = $to->diffInDays($from);


        // calculate the price of reservation using the price of the chosen vehicle
        $price = $diff_in_days * Vehicle::find($request->vehicle_id)->price_per_day;
        $request->total_price = $price;
        $data = array_merge($request->except('equipment'), ['total_price' => $price]);
        $reservation = Reservation::query()->create($data);

        foreach ($request->equipment as $item){
            EquipmentReservation::query()->create([
                 'equipment_id'=>$item['equipment_id'],
                 'reservation_id'=>$reservation->id,
                 'quantity'=>$item['quantity']
            ]);
        }

        $reservation = Reservation::query()->find($reservation->id);

        return response()->json($reservation,200);

    }


    public function show($id)
    {

        $res = Reservation::query()->where('id','=',$id)->with([
            'rentLocation' => function($query){
                $query->select(['id','name']);
            },
            'returnLocation' => function($query){
                $query->select(['id','name']);
            },
            'vehicle' => function($query){
                $query->with('carType');
            },
            'equipment',
            'client'
        ]);

        $res = $res->first();
        return response()->json($res);
    }


    public function update(ReservationRequest $request, $id)
    {

        //dd(Vehicle::query()->find($request->vehicle_id)->isAvailable($request->from_date,$request->to_date,$id));
        abort_unless(Vehicle::query()->find($request->vehicle_id)->isAvailable($request->from_date,$request->to_date,$id),422,'Vehicle is unavaliable');
        $request->to_date .= ' 00:00:00';
        $request->from_date .= ' 00:00:00';

        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $request->to_date);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $request->from_date);
        $diff_in_days = $to->diffInDays($from);


        // calculate the price of reservation using the price of the chosen vehicle
        $price = $diff_in_days * Vehicle::find($request->vehicle_id)->price_per_day;
        $request->total_price = $price;
        $data = array_merge($request->except('equipment'), ['total_price' => $price]);
        $reservation = Reservation::query()->where('id','=',$id)->update($data);

        EquipmentReservation::query()->where('reservation_id' ,$id)->delete();

        foreach ($request->equipment as $item){
            EquipmentReservation::query()->create([
                'equipment_id'=>$item['equipment_id'],
                'reservation_id'=>$id,
                'quantity'=>$item['quantity']
            ]);
        }

        $reservation = Reservation::query()->find($id);

        return response()->json($reservation,200);


    }


    public function destroy($id)
    {
        //dd($id);
        $equipment = EquipmentReservation::query()->where('reservation_id','=',$id)->first();
        if ($equipment){
            EquipmentReservation::query()->where('reservation_id','=',$id)->delete();
        }
        Reservation::query()->where('id','=',$id)->delete();
        return response()->json(['message'=>'Success!'],200);
    }
}
