<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class HomeController extends Controller
{
    public function Register(Request $request){
        try{
            // $validateUser = Validator::make($request->all(),[
            //     'name' => 'required',
            //     'email' => 'required|email|unique:users,email',
            //     'password' => 'required',
            // ]);

            // if($validateUser->fails()){
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'validation error',
            //         'errors'=> $validateUser->errors()
            //     ], 401);
            // }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token'=> $user->createToken("API TOKEN")->plainTextToken,
               //  'referral_count' => $user->referred_by ? $referrer->referral_count : 0
            ], 200);

        }
        catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function Login(Request $request){

        $validateUser = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors'=> $validateUser->errors()
            ], 401);
        }

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'status' => false,
                'message' => 'Email & Password does not match with our record.',
                'data' => [
                    'email' => $request->email,
                    'password' => $request->password,
                ]
            ], 401);
        }

        $user = Auth::user();

        $redirect_url = $user->role === '1' ? '/admin' : '/user';

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully',
            'token'=> $user->createToken("API TOKEN")->plainTextToken,
            'redirect_url' => $redirect_url
        ]);

    }
}
