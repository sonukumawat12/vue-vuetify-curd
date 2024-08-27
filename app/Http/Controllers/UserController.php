<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers()
    {
        $usersData= User::latest()->get();
        return response()->json($usersData);
    }


    public function updateUser(Request $request ,$id)
    {
        $user= User::find($id);
        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    public function Destroy(Request $request ,$id)
    {
        $user= User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }


    public function Store(Request $request)
    {
        $user = new User();
        $user->create($request->all());
        return response()->json(['message' => 'User created successfully']);
    }


}
