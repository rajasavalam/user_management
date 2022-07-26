<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use DB, Auth, URL;


class LoginController extends Controller
{
    //
    public function index(){
        if(Auth::check()){
            return view('users_list');    
        }
        return view('login');
    }

    /**
     * users edit functionality
     */

    public function post_login(Request $request){
        $input = $request->all();
        $validate_arr['email'] = ['required','email'];
        $validate_messages['email.required'] = 'Please enter email id';
        $validate_messages['email.email'] = 'Please enter valid email id';

        $validate_arr['password'] = 'required';
        $validate_messages['password.required'] = 'Please enter password';

        $validatedData = $request->validate($validate_arr, $validate_messages);
        $cred['email_id'] = $input['email'];
        $cred['password'] = $input['password'];
        if(Auth::guard('web')->attempt($cred,request()->filled('remember'))){
			$user = auth('web')->user();
            $redirect = URL::to('/list-users');
			$response= ['status'=>true,'redirect'=>$redirect ];
            return $response;
        }
        else{
			$response= ['message'=>'Email ID or Password is invalid','status'=>false ];
            return $response;
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }
    
}
