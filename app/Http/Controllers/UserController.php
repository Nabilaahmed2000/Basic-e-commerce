<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;


class UserController extends Controller
{
    //display all users api
    public function index()
    {
        $users = User::all();
        return UserResource::collection($users);
    }
    //display single user api
    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }
    //create user api
    public function store(Request $request)
    {
        $user = User::create(
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]
        );
        if ($request->hasFile('profile_img')) {
            $image = $request->file('profile_img');
            // Store the image using the media library
            $user->addMedia($image)->toMediaCollection('profile_imges');
        }
        return new UserResource($user);
    }
    //update user api
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return new UserResource($user);
    }
    //delete user api
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return new UserResource($user);
    }
    //Api for blocking customers 
    public function block($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_blocked' => true]);
        return new UserResource($user);
    }
}
