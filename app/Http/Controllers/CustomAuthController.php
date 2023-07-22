<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Session;

class CustomAuthController extends Controller
{
    public function login()
    {
        return  view("auth.Login");
    }
    public function registration()
    {
        return view("auth.Registration");
    }
    public function registerUser(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:5|max:12'
        ]);
        $user= new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $result=$user->save();
        if ($result) {
            return back()->with('Success', 'You are registered');
        }else{
            return back()->with('fail', 'Something went wrong');
        }
    }
    public function loginUser(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:5|max:12'
        ]);
        $user=User::where('email', '=', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $request->session()->put('loginId', $user->id);
                return redirect('dashboard');
            } else {
                return back()->with('fail', 'Password is incorrect.');
            }
            
        } else {
             return back()->with('fail', 'This email is not registered.');
        }
        
    }
    public function dashboard()
    {
        $data= array();
        if (Session::has('loginId'))
        {
            $data=User::where('id', '=', Session::get('loginId'))->first(); 
        }
       return view('dashboard', compact('data')); 
    }
    public function logout()
    {
        if (Session::has('loginId'));
        Session::pull('loginId');
        return redirect('login');
    }
}

