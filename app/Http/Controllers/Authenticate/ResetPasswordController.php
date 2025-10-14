<?php

namespace App\Http\Controllers\Authenticate;

use Carbon\Carbon;
use Mail;
use Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $email_data = [

            "user" => $user,
            "token" => $token,

        ];

        Mail::send('emails.reset_password', $email_data, function ($message) use ($user) {
            $message->to($user->email, $user->name);
            $message->subject('Password Rest Request');
        });

        return response()->json([
            'status' => true,
            'message' => "Email has been send",
        ], 200);


    }
}
