<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Product;
use App\Category;
use App\User;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::selectRaw('COALESCE(sum(CASE WHEN status = 4 THEN subtotal + cost END), 0) as turnover, 
        COALESCE(count(CASE WHEN status = 0 THEN subtotal END), 0) as newOrder,
        COALESCE(count(CASE WHEN status = 2 THEN subtotal END), 0) as processOrder,
        COALESCE(count(CASE WHEN status = 3 THEN subtotal END), 0) as shipping,
        COALESCE(count(CASE WHEN status = 4 THEN subtotal END), 0) as completeOrder')->get();

        $customers = Customer::get();
        $categories = Category::get();
        $products = Product::get();
        
        return view('home', compact('orders','customers', 'categories', 'products'));
    }

    public function settingAcount($id){
        $user = User::find($id);
        return view('setting.setting', compact('user'));
    }
    
    public function updateAcount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'password' => 'nullable|string|min:5'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }
    
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
            }
            $data = $request->only('name', 'email');
    
            if ($request->password != '') {
                $data['password'] = $request->password;
            }

            $user->update($data);

            return response()->json(['success' => 'Profil berhasil diperbarui', 'redirect' => route('home')], 200);
            // return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }
}
