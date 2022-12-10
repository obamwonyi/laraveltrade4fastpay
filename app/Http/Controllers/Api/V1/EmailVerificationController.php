<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    //
 
    use HttpResponses;



    //sending a mail for email verification to the registered user . 
    public function sendVerifyMail($email)
    {


        //fetch the user details with the email $email 
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
                404
                );

        }

        //generate a random string to be used as token
        $random = Str::random(40);

        //creates a url 
        $domain = URL::to('/');
        $url = $domain."/V1/verify-mail/".$random;

        $data['url'] = $url;
        $data['email'] = $email;
        $data['title'] = "Email Verification";
        $data['body'] = "Please click to verify your mail.";


        //sends the mail using the verifyMail.blade.php as the format ('verifyMail')
        Mail::send('verifyMail',['data'=>$data], function($message)use($data)
        {
            $message->to($data['email'])->subject($data['title']);
        });


        //finds the user with the id 
        $user = User::find($user[0]['id']);


        //store the email verification details in 
        //the email verification database . 
        DB::table('email_verifications')->insert([
            "user_id" => $user->id,
            "token" => $random,
            "expires_at" => Carbon::now()->addHour(),
            "created_at" => Carbon::now()
        ]);


        //create the token for the user 
        $user->remember_token = $random;

        $user->save();


        return response()->json(
            [
                "status" => 'success',
                "message" => "Mail sent successfully",
                "data" => null
            ],
            200
            );

    }


    //this method is responsible for verifying the email 
    public function verifyMail($token)
    {

        // //trimming the token cause it has a white space in it . 
        // $token=trim($token);

        //checking if the remember token of this current user is equal to that from the url 
        $user = User::where('remember_token',$token)->get();


        if(count($user) < 1)
        { //return view('404');

            return response()->json(
                [
                    "status" => 'failed',
                    "message" => "Invalid Token",
                    "data" => null
                ],
                403
                );
        }

        $email_verification = DB::table('email_verifications')->where('token',$token)->get();



        $t2 = strtotime(($email_verification[0])->expires_at);
        $t1 = strtotime(Carbon::now()->format('Y-m-d H:i:s'));
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

        $user = User::find($user[0]['id']);

        //update the remember_token column for this particular user to be empty as a sign of complete registration 
        $user->remember_token = '';
        //update is_verified column for this particular user to be 1(true)
        $user->is_verified = 1;
        //set the time it was verified at as the time the email was clicked . 
        $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();



        //returning a temporal view to signify email verification 
        return view('verificationSuccess');

    }



}
