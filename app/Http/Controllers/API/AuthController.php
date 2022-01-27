<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Auth;
use App\Http\Traits\ApiMessagesTrait;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiMessagesTrait;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        try{
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials))
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            $user = $request->user();
            // dd($user);
            $tokenResult = $user->createToken('Personal Access Token');
            // dd($tokenResult);
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
            if($user->company == NULL){
                $img_path=asset('user_images/');
                $user->img=$img_path.'/'.$user->img;
            }else{
                $img_path=asset('company_images/');
                $user->img=$img_path.'/'.$user->img;
            }

            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user' => $user
            ]);

        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function logintest(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        try{
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials))
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            $user = $request->user();

            /*$tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;*/

            /*if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();*/
            $img_path=asset('user_images/');
            $user->img=$img_path.'/'.$user->img;
            //dd('here');
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                /*'token' => $tokenResult->accessToken,*/
                'token_type' => 'Bearer',
                /*'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),*/
                'user' => $user
            ]);

        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
}
