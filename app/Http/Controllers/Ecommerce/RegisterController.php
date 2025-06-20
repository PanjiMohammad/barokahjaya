<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Customer;
use App\Province;
use App\Mail\CustomerRegisterMail;
use Mail;

class RegisterController extends Controller
{
    public function registerForm()
    {
        if (auth()->guard('customer')->check()) return redirect(route('customer.dashboard'));

        $provinces = Province::orderBy('created_at', 'DESC')->get();
        return view('ecommerce.register', compact('provinces'));
    }


    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:100',
            'password' => 'required|string|min:5',
            'customer_phone' => 'required',
            'email' => 'required|email',
            'customer_address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        try {
            if (!auth()->guard('customer')->check()) {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'email' => $request->email,
                    'password' => $request->password, 
                    'phone_number' => $request->customer_phone,
                    'address' => $request->customer_address,
                    'district_id' => $request->district_id,
                    'activate_token' => null,
                    'status' => true
                ]);
            }

            // if (!auth()->guard('customer')->check()) {
            //     Mail::to($request->email)->send(new CustomerRegisterMail($customer, $password));
            // }
            return response()->json(['success' => 'Registrasi Member Berhasil'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
