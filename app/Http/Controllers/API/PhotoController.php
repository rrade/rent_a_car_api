<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Role;
use Illuminate\Http\Request;

class PhotoController extends Controller
{

    public function destroy($id)
    {
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }

        Photo::query()->where('id',$id)->delete();
        return response()->json(['message'=>'Success!'],200);
    }
}
