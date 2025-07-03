<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Province;
use App\City;
use App\District;
use App\Customer;
use App\Order;
use App\OrderDetail;
use Illuminate\Support\Str;
use DB;
use App\Mail\CustomerRegisterMail;
use Mail;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private function getCarts()
    {
        $carts = json_decode(request()->cookie('e-carts'), true);
        $carts = $carts != '' ? $carts:[];
        return $carts;
    }

    public function addToCart(Request $request)
    {
        try {
            $this->validate($request, [
                'product_id' => 'required|exists:products,id', 
                'qty' => 'required|integer' 
            ]);

            $carts = json_decode($request->cookie('e-carts'), true); 
            
            // get customer
            $customer = auth()->guard('customer')->user();

            if (!$customer) {
                return response()->json(['error' => 'Harap login / registrasi terlebih dahulu untuk melanjutkan transaksi', 'redirect' => route('customer.login')], 401);
            }
            // Load the 'district' relationship only if the customer exists.
            $customer->load('district');
        
            if ($carts && array_key_exists($request->product_id, $carts)) {
                $carts[$request->product_id]['qty'] += $request->qty;
            } else {
                $product = Product::find($request->product_id);
                $carts[$request->product_id] = [
                    'qty' => $request->qty,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'product_image' => $product->image,
                    'weight' => $product->weight
                ];
            }

            $cookie = cookie('e-carts', json_encode($carts), 2880);
            return response()->json(['success' => 'Berhasil menambahkan produk ke keranjang'])->cookie($cookie);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function listCart()
    {
        $carts = $this->getCarts();
        $subtotal = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['product_price']; 
        });

        return view('ecommerce.cart', compact('carts', 'subtotal'));
    }

    public function updateCart(Request $request)
    {
        try {
            $carts = $this->getCarts();

            $productId = $request->input('product_id');
            $qty = (int) $request->input('qty');

            if (!$productId || !isset($carts[$productId])) {
                return response()->json(['error' => 'Produk tidak ditemukan dalam keranjang.'], 404);
            }

            if ($qty <= 0) {
                unset($carts[$productId]);
            } else {
                $carts[$productId]['qty'] = $qty;
            }

            // Hitung ulang subtotal
            $subtotal = collect($carts)->sum(function ($item) {
                return $item['product_price'] * $item['qty'];
            });

            $cookie = cookie('e-carts', json_encode($carts), 2880); // 2 hari

            return response()->json([
                'success' => 'Keranjang berhasil diperbarui',
                'subtotal' => $subtotal,
                'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.')
            ])->cookie($cookie);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCart(Request $request)
    {
        $carts = $this->getCarts(); 
        $cookie = cookie('e-carts', json_encode([]));

        return response()->json(['success' => true, 'message' => 'Pesanan dalam keranjang berhasil dihapus.'])->withCookie($cookie);
    }

    public function getCourier(Request $request)
    {
        $this->validate($request, [
            'destination' => 'required',
            'weight' => 'required|integer'
        ]);
        
        $url = 'https://ruangapi.com/api/v1/shipping';
        $key = env('RUANGAPI_KEY');

        $response = Http::withHeaders([
            'Authorization' => $key
        ])->post($url, [
            'origin' => 22, 
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => 'jnt,tiki,sicepat' 
        ]);
        
        $body = json_decode($response->getBody(), true);
        return $body;
    }

    public function checkout()
    {
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        $carts = $this->getCarts(); 

        if (empty($carts) || count($carts) === 0) {
            return redirect(route('front.index'))->with(['error' => 'Silahkan pilih barang untuk melanjutkan transaksi']);
        }

        // set customer
        $customer = null;
        if(auth()->guard('customer')->check()){
            $customer = auth()->guard('customer')->user()->load('district');
        }

        $subtotal = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['product_price'];
        });
        $weight = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['weight'];
        });
        return view('ecommerce.checkout', compact('provinces', 'carts', 'subtotal', 'weight', 'customer'));
    }

    public function getCity()
    {
        $cities = City::where('province_id', request()->province_id)->get();
        return response()->json(['status' => 'success', 'data' => $cities]);
    }

    public function getDistrict()
    {
        $districts = District::where('city_id', request()->city_id)->get();
        return response()->json(['status' => 'success', 'data' => $districts]);
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required',
            'email' => 'required|email',
            'customer_address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'courier' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        DB::beginTransaction();
        try {
            
            $customer = Customer::where('email', $request->email)->first();
            if (!auth()->guard('customer')->check() && $customer) {
                return redirect()->back()->with(['error' => 'Silahkan Login Terlebih Dahulu']);
            }

            $carts = $this->getCarts();

            $subtotal = collect($carts)->sum(function($q) {
                return $q['qty'] * $q['product_price'];
            });

            // $shipping = explode('-', $request->courier);
            $order = Order::create([
                'invoice' => strtoupper(Str::random(4)) . '-' . Carbon::now('Asia/Jakarta')->format('YmdHis'), 
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'district_id' => $request->district_id,
                'subtotal' => $subtotal,
                'cost' => 25000, 
                'shipping' => $request->courier
            ]);

            foreach ($carts as $row) {
                $product = Product::find($row['product_id']);
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $row['product_id'],
                    'price' => $row['product_price'],
                    'qty' => $row['qty'],
                    'weight' => $product->weight
                ]);
            }
            
            DB::commit();

            $carts = [];
            $cookie = cookie('e-carts', json_encode($carts), 2880);
            // return redirect(route('front.finish_checkout', $order->invoice))->cookie($cookie);
            return response()->json(['success' => 'Berhasil checkout pesanan', 'redirect' => route('front.finish_checkout', $order->invoice)])->cookie($cookie);
        } catch (\Exception $e) {
            DB::rollback();
            // return redirect()->back()->with(['error' => $e->getMessage()]);
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function checkoutFinish($invoice)
    {
        $order = Order::with(['district.city'])->where('invoice', $invoice)->first();
        if (Order::where('invoice', $invoice)->exists()){
            return view('ecommerce.checkout_finish', compact('order'));
        }else {
            return redirect()->back();
        }    
    }
}
