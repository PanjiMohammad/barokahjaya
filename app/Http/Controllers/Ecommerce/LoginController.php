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
        if (auth()->guard('customer')->check()) return redirect(route('customer.dashboard'));
        return view('ecommerce.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $auth = $request->only('email', 'password');
        $auth['status'] = 1; 
    
        if (auth()->guard('customer')->attempt($auth)) {
            return redirect()->intended(route('customer.dashboard'));
        }

        return redirect()->back()->with(['error' => 'Email / Password Salah']);
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
        auth()->guard('customer')->logout(); 
        return redirect(route('customer.login'));
    }
}
