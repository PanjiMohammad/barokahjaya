<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Province;
use App\User;
use Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    // /**
    //  * Where to redirect users after login.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = RouteServiceProvider::HOME;

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

    public function showLoginForm(){
        return view('auth.login');
    }

    public function newRegister(){
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        return view('auth.register', compact('provinces'));
    }

    public function postRegister(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone_number' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB max size
        ]);

        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors(), 'message' => 'Gagal Menyimpan', 'input' => $request->all()], 400);
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        try {
            if (Seller::where('email', $request->email)->exists()) {
                return response()->json(['error' => 'Email Sudah Ada, Silahkan Coba Lagi.'], 409);
            }

            $password = Str::random(8); 
            $seller = Seller::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password, 
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'district_id' => $request->district_id,
                'activate_token' => Str::random(30),
                'status' => false
            ]);

            Mail::to($request->email)->send(new SellerRegisterMail($seller, $password));

            return response()->json(['success' => true, 'message' => 'Registrasi Berhasil, Silahkan Cek Email.'], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan, silakan coba lagi.'], 500);
        }
    }

    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        $superAdminExists = User::where('email', $request->email)->first();

        if (!$superAdminExists) {
            return response()->json(['error' => 'Email tidak terdaftar'], 400);
        }

        // Credentials for authentication
        $credentials = $request->only('email', 'password');

        if($superAdminExists && auth()->guard('web')->attempt($credentials)){
            return response()->json(['success' => 'Login berhasil', 'redirect' => route('home')], 200);
        }

        return response()->json(['error' => 'Email atau password salah, silakan coba lagi.'], 401);
    }

    public function resetPasswordUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Cari user berdasarkan ID
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            // Hash password baru menggunakan bcrypt
            $hashedPassword = bcrypt('admin');
            
            // Update password user
            $user->password = $hashedPassword;
            $user->save();
            
            return response()->json(['success' => 'Password berhasil direset'], 200);
        }

        return response()->json(['error' => 'User tidak ditemukan'], 404);;

    }

    public function logout(Request $request){
        Auth::logout();
        return response()->json([
            'success' => 'Berhasil keluar dari halaman', 
            'redirect' => route('login')
        ], 200);
    }
}
