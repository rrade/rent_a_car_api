<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    const PER_PAGE = 10;

    protected $guarded = [];
    protected $with = ['carType','photos'];
    protected $hidden = ['created_at', 'updated_at'];

    public function carType(){
        return $this->belongsTo(CarType::class);
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    public function photos() {
        return $this->hasMany(Photo::class);
    }

    public function scopeAvailable($query,$start_date,$end_date)
    {
        return $query->whereNotExists(function($q) use ($start_date,$end_date){
            $q->from('reservations')
                ->whereRaw('reservations.vehicle_id = vehicles.id')
                ->where(function ($query) use ($start_date,$end_date){
                        $query->whereBetween('from_date', [$start_date, $end_date])
                        ->orWhereBetween('to_date', [$start_date, $end_date])
                        ->orWhereRaw('? BETWEEN from_date and to_date', [$start_date])
                        ->orWhereRaw('? BETWEEN from_date and to_date', [$end_date]);
                });
        });
    }

    public function isAvailable($start_date,$end_date,$id = null)
    {
      return Reservation::query()->whereBetween('from_date', [$start_date, $end_date])
          ->orWhereBetween('to_date', [$start_date, $end_date])
          ->orWhereRaw('? BETWEEN from_date and to_date', [$start_date])
          ->orWhereRaw('? BETWEEN from_date and to_date', [$end_date])->where('vehicle_id',$this->id)->when($id,function ($query) use ($id){
              $query->where('reservations.id','!=',$id);
              })->count() == 0;

    }

}
