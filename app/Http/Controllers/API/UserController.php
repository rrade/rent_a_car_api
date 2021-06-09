<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserRequest;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
       // dd(auth()->user()->role_id);
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }
        $res = User::query()->with('role')
            ->when($request->search, function($query) use ($request){
            $term = strtolower($request->search);
            //dd($term);
            $query->whereRaw("lower(name) LIKE '%{$term}%' ")
                ->orWhereRaw("lower(email) LIKE '%{$term}%' ");
        });

        $res = $res->paginate(User::PER_PAGE);
        return response()->json($res);

    }


    public function store(UserRequest $request)
    {
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }
        //$data = array_merge($request->validated(), ['total_price' => $price]);
        $client = Client::query()->create($request->validated());
        $user_created = User::query()->create([
            'email'=>$request->email,
            'name' =>$request->name,
            'role_id' =>Role::USER,
            'client_id' =>$client->id,
            'password' => Hash::make('12345678'),
            ]);


        $user = User::query()->where('id',$user_created->id)->with(['client'])->first();
        return response()->json($user);


    }


    public function show($id)
    {
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }
        $user = User::query()->where('id',$id)->with(['client'])->first();
        return response()->json($user);

    }


    public function update(UserRequest $request, $id)
    {

        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }

        $user = User::query()->where('id',$id);
        $user_get =$user->first();

        $validated = $request->validate([

            'email' => "required|email|unique:clients,email,$user_get->client_id|unique:users,email,$user_get->id",
            'identification_document_no' =>  "required|alpha_num|unique:clients,identification_document_no,$user_get->client_id",
            'phone_no'=> "required|numeric|unique:clients,phone_no,$user_get->client_id"

        ]);

        $data = array_merge($validated, $request->validate($request->rules()));

        $client = Client::query()->where('id',$user_get->client_id)->update($data);
        $user_updated = $user->update([
            'email'=>$request->email,
            'name' =>$request->name,
        ]);


        $user = $user->with(['client'])->first();
        return response()->json($user);


    }


    public function destroy($id)
    {
        if (auth()->user()->role_id == Role::USER){
            return response()->json(['message'=>'Unauthorised!'],403);
        }
        $user = User::query()->where('id',$id);
        $user_get =$user->first();
        $user_get = $user_get->client_id;
        $user->delete();
        Client::query()->where('id',$user_get)->delete();
        return response()->json(['message'=>'Success!'],200);

    }
}
