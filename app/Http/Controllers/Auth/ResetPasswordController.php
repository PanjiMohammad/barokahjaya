<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    public function forgotPasswordForm(){
        return view('auth.passwords.reset');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors(), 'message' => 'Gagal Menyimpan', 'input' => $request->all()], 400);
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        // // Cari user berdasarkan ID
        // $user = User::where('email', $request->email)->first();
        
        // if ($user) {
        //     // Hash password baru menggunakan bcrypt
        //     $hashedPassword = bcrypt('admin');
            
        //     // Update password user
        //     $user->password = $hashedPassword;
        //     $user->save();
            
        //     return 'Password berhasil diupdate!';
        // }

        // return 'User tidak ditemukan!';

        try {
            $data = Seller::where('email', $request->email)->first();

            if($data != null){
                $seller = Seller::find($data->id);
                $password = Str::random(8); 
                $seller->update([
                    'password' => $password,
                    'activate_token' => Str::random(30),
                    'status' => 0
                ]);

                Mail::to($request->email)->send(new SellerResetPasswordMail($seller, $password));

                return response()->json(['success' => 'Atur Ulang Kata Sandi Berhasil, Silahkan Cek Email.'], 200);
            } else {
                return response()->json(['error' => 'Atur Ulang Kata Sandi Gagal, Email Tidak Terdaftar.'], 409);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi Kesalahan : ' . $e->getMessage()], 500);
        }
    }
}
