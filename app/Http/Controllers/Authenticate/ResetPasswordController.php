<?php

namespace App\Http\Controllers\Authenticate;

use App\Mail\ResetPasswordMail;
use Str;
use Mail;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class ResetPasswordController extends Controller
{
    public function reset_password_request(Request $request)
    {

        $user_email = $request->email;

        $user = User::where('email', $user_email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "User not found",
            ], Response::HTTP_NOT_FOUND);
        }

        $token = Str::random(60);

        $exists = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->exists();

        if ($exists) {
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->update([
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now(),
                ]);
        } else {
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);
        }

        // $email_data = [

        //     "user" => $user,
        //     "token" => $token,

        // ];

        // Mail::send('emails.reset_password', $email_data, function ($message) use ($user) {
        //     $message->to($user->email, $user->name);
        //     $message->subject('Password Rest Request');
        // });

        // === By Mailer
         
        Mail::to($user_email)->send(new ResetPasswordMail($user,$token));

        return response()->json([
            'status' => true,
            'message' => "Email has been send",
        ], 200);


    }

    public function reset_password(Request $request){
            
        try{
            // taking inputs form user 
            $email = $request->email;
            $new_password = $request->password;
            $token = $request->token;

            // check the user exixst or not 
            $user = User::where('email',$email)->first();

            if(!$user){
                return response()->json([
                        'status' =>false,
                        'message' =>"User is not in Database",
                ],500);
            }

            // Check token table see the token available or not

            $token_verified = DB::table('password_reset_tokens')->where('email',$email)->first();

            if(!$token_verified || ($token != $token_verified->token)){
                 return response()->json([
                        'status' =>false,
                        'message' =>"Token not matched",
                ],500);
            }

            // time check
            if(Carbon::parse($token_verified->created_at)->addHour()->isPast()){
                DB::table('password_reset_tokens')->where('token',$token_verified->token)->delete();
                  return response()->json([
                        'status' =>false,
                        'message' =>"Token expired",
                ],500);
            }

            // if all is ok 
            // change the password and remove the token ,,, 
            User::where('email',$email)->update([
                    'password' => $new_password,
                    'created_at' => Carbon::now(),
            ]);
             DB::table('password_reset_tokens')->where('token',$token_verified->token)->delete();

                 return response()->json([
                        'status' =>false,
                        'message' =>"Password Has been Changed",
                ],200);



        }catch(Throwable $e){
            Log::channel('reset_password_error')->error('reset_password failed',[
                   'message' =>$e->getMessage(),
                   'line' =>$e->getLine(),
            ]);
        }
    }
}
