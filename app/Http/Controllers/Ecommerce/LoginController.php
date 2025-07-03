<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use App\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Mail;

class LoginController extends Controller
{
    public function loginForm()
    {
        // if (auth()->guard('customer')->check()) {
        //     return redirect()->intended(route('customer.dashboard'));
        // }

        // $previous = url()->previous();

        // if (!session()->has('url.intended') && !Str::contains($previous, 'login')) {
        //     session(['url.intended' => $previous]);
        // }

        if(auth()->guard('customer')->check()){
            return redirect(route('front.index'))->with(['error' => 'Anda tidak bisa memasuki halaman Login']);
        }
        return view('ecommerce.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1;

        $customer = Customer::where('email', $request->email)->first();

        if($customer === null) {
            return response()->json(['error' => 'Akun tidak terdaftar, Silahkan registrasi member.'], 404);
        } else if($customer->status !== 1) {
            return response()->json(['error' => 'Terjadi kesalahan saat mencoba masuk, Silahkan hubungi admin.'], 404);
        } else {
            //
        }

        if (auth()->guard('customer')->attempt($credentials)) {
            return response()->json(['success' => 'Login berhasil', 'redirect' => route('customer.dashboard')], 200);
        } else {
            return response()->json(['error' => 'Email / Password Salah'], 401);
        }
    }

    public function forgotPassword()
    {
        return view('ecommerce.forgotpassword');
    }

    public function ResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors(), 'message' => 'Gagal Menyimpan', 'input' => $request->all()], 400);
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        try {
            $customer = Customer::where('email', $request->email)->first();
            
            if ($customer) {
                // Hash password baru menggunakan bcrypt
                $hashedPassword = 'member123';
                
                // Update password user
                $customer->password = $hashedPassword;
                $customer->save();
                
                return response()->json(['success' => 'Password berhasil diupdate menjadi member123'], 200);
            }

            return response()->json(['error' => 'Pengguna tidak ditemukan!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function dashboard()
    {
        //Terdapat kondisi dengan menggunakan CASE, dimana jika kondisinya terpenuhi dalam hal ini status 
        //maka subtotal akan di-sum, kemudian untuk shipping dan complete hanya di count order

        $orders = Order::selectRaw('COALESCE(sum(CASE WHEN status = 0 THEN subtotal + cost END), 0) as pending, 
        COALESCE(count(CASE WHEN status = 3 THEN subtotal END), 0) as shipping,
        COALESCE(count(CASE WHEN status = 4 THEN subtotal END), 0) as completeOrder')
        ->where('customer_id', auth()->guard('customer')->user()->id)->get();

        return view('ecommerce.dashboard', compact('orders'));
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return response()->json(['success' => 'Berhasil Logout', 'redirect' => route('customer.login')], 200);
    }
}
