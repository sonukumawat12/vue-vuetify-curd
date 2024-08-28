<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $imageName = time() . '_' . $image->getClientOriginalName();
           File::delete($user->profile);
      
            $image->move(public_path('images/products'), $imageName);
            $user->profile ='images/products/'. $imageName;
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
       
        $user->save();
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
        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $user->profile ='images/products/'. $imageName;
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
       
        $user->save();
    
        return response()->json(['message' => 'User created successfully']);
    }

    public function Dashboard()
    {
        return view('./dashboard');
    }
    public function imageUpload()
    {
        return view('./img');
    }




}
