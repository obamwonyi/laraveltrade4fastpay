<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;



class AuthController extends Controller
{
    //

    use HttpResponses;

    //method to login user
    public function login(LoginUserRequest $request)
    {
        //check if all the fields are valid . 
        $request->validated($request->all());

        //returns a failed response when the email does not 
        //match the password . 
        if(!Auth::attempt($request->only('email','password')))
        {
            return $this->failed('','Credentials do not match any user', 401);
        }


        $user = User::where('email', $request->email)->first();

        //return an unauthorized when an email is not verified 
        if($user->is_verified === 0)
        {
            return $this->failed("","Email not verified",401);
        }


        return response()->json(
            [
                "status" => 'success',
                "message" => "login successfull",
                "data" => [
                    "user" => $user, 
                    "token" => $user->createToken('Api Token of'. $user->name)->plainTextToken
                ]
            ],
            200
            );

    }


    //-------------------------------------------------------------------------------


    //method to register new users
    public function register(StoreUserRequest $request)
    {
        //validating the form field
        $request->validated($request->all());

        //creating a new user after the filed 
        //has been validated
        $user = User::create(
            [
                'first_name' => $request->first_name, 
                'last_name' => $request->last_name, 
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]
            );

            //return user response after successful registration 
            return $this->success(
                //section for data response 
                [
                    "verify mail at" => "localhost/api/V1/send-verify-mail/{$user->email}",
                ],
                //message response 
                "User Registration Was Successful (unverified, no login access yet)"
                );



    }


    public function logout() 
    {

        //if the current user on the request has a token 
        //then delete it and log them out . 
        if(Auth::user()->currentAccessToken())
        {
            Auth::user()->currentAccessToken()->delete();
        }
        return $this->success(
            [
                "data" => null
            ],
            "You have successfully been logged out and your token has been deleted"
            );  
    }

    //---------------------------------------------------------------------------------

    //handle forgotPassword functionality 
    public function forgotPassword($email) 
    {
        $user = User::where('email', $email)->get();

        //return a fail message when no users are found with the email 
        if(count($user) < 1)
        {


            return response()->json(
                [
                    "status" => 'failed',
                    "message" => "User is not found",
                    "data" => null
                ],
                403
                );
        }

        //generate a random string to be used as token
        $random = Str::random(40);

        //creates a url 
        $domain = URL::to('/');
        $url = $domain."/api/V1/reset-password/".$random;

        $data['url'] = $url;
        $data['email'] = $email;
        $data['title'] = "Password reset token";
        $data['body'] = "Please click the link below to reset your password";
        //sends the mail using the resetPassword.blade.php as the format ('resetPassword')


        Mail::send('resetPassword',['data'=>$data], function($message)use($data)
        {
            $message->to($data['email'])->subject($data['title']);
        });


        //finds the user with the id 
        //$user = User::find($user[0]['id']);

        //fetching the password_resets table . 
        $passwordReset = DB::table('password_resets');

        $passwordReset->insert([
            'email' => $email, 
            'token' => $random,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);


        return response()->json(
            [
                "status" => 'success',
                "message" => "Password reset link has been sent to {$email}",
                "data" => null
            ],
            200
            );
    }

    //---------------------------------------------------------------------------

    //handle resetPassword functionality 
    public function resetPassword($token,Request $request)
    {
        //checking if the remember token of this current user is equal to that from the url 
        $passwordReset = DB::table('password_resets')->where('token', $token)->get();



        if(count($passwordReset) < 1)
        {
            return response()->json(
                [
                    "status" => 'failed',
                    "message" => "Unrecognized token and access",
                    "data" => null
                ],
                403
                );
        }



        $t1 = strtotime( ($passwordReset[0])->created_at );
        $t2 = strtotime(Carbon::now()->format('Y-m-d H:i:s'));
        //round() the difference of the stored time and the current 
        //time divided by 3600 
        $diff = round(($t2 - $t1) / 3600);

        //if the round() of the time is greater than 
        //one hour send a token has expired response .

        if($diff > 1 or $diff < 0)
        {

            return response()->json(
                [
                    "status" => 'failed',
                    "message" => "Token has expired",
                    "data" => null
                ],
                403
                );
        }

        $passwordReset = $passwordReset[0];

        //finally update the password column of the user 
        //with the particular mail passed . 
        DB::table('users')
            ->where('email', $passwordReset->email)
            ->update(['password' => Hash::make($request->password)]);
        //dd($user);


        return response()->json(
            [
                "status" => 'success',
                "message" => "Password reset successful",
                "data" => null
            ],
            200
            );

    }
}
