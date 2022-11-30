<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController
{
    public static function index(Request $request)
    {
        $user = User::query();
        $name = $request->input('filter.name');
        if(!empty($name))
        {
            $user->where('user.name', '=', $name);
        }
        $email = $request->input('filter.email');
        if(!empty($email))
        {
            $user->where('users.email', '=', $email);
        }
        $country = $request->input('filter.country');
        if(!empty($country))
        {
            $user->where('users.country_id', '=', $country);
        }
        $users =$user->get();
        return UserResource::collection($users);
    }
    public static function create(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'country_id' => ['required']
        ]);
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "country_id" => $request->country_id,
            "password" => Hash::make('password'),
            "remember_token" => Str::random(10)
        ]);
        return $user->createToken('default_device')->plainTextToken;
    }
    public static function edit(Request $request)
    {
        $user = User::find($request->id);

    }
}
