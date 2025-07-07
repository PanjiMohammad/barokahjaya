<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Payment;
use Carbon\Carbon;
use DB;
use PDF;
use App\OrderReturn;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DataTables;

class OrderController extends Controller
{
    public function index()
    {
        return view('ecommerce.orders.index');
    }

    public function getIndexDatatables(Request $request)
    {
        // get customer id
        $customerIds = auth()->guard('customer')->user()->id;

        $orders = Order::withCount(['return'])->where('customer_id', $customerIds)->orderBy('created_at', 'DESC')->get();

        return DataTables::of($orders)
            ->addColumn('action', function ($order) use (&$index) {
                static $index = 0;
                $index++;

                $detailUrl = route('customer.view_order', $order->invoice);
                $returnUrl = route('customer.order_return', $order->invoice);
                $acceptForm = '';

                if ($order->status == 3 && $order->return_count == 0) {
                    $acceptForm = '
                        <form action="' . route('customer.order_accept') . '" id="acceptOrder" method="POST">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-success btn-sm mr-1" data-order-id="'.$order->id.'" title="Terima pesanan '. $order->invoice .'">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <a href="' . $returnUrl . '" class="btn btn-danger btn-sm" title="Kembalikan pesanan '. strtoupper($order->invoice) .'">
                            <i class="fas fa-xmark"></i>
                        </a>
                    ';
                }

                return '
                    <div class="d-flex align-items-center">
                        <a href="' . $detailUrl . '" class="btn btn-primary btn-sm mr-1" title="Lihat detail pesanan '. strtoupper($order->invoice) .'">
                            <i class="fas fa-eye"></i>
                        </a>
                        ' . $acceptForm . '
                    </div>
                ';
            })
            ->editColumn('invoice', function($order){
                return strtoupper($order->invoice);
            })
            ->editColumn('details', function ($order) {
                return $order->customer_name;
            })
            ->editColumn('amount', function ($order) {
                return 'Rp ' . number_format($order->total, 0, ',', '.');
            })
            ->addColumn('status', function ($order) {
                return $order->status_label;
            })
            ->editColumn('dates', function ($order) {
                return Carbon::parse($order->created_at)->locale('id')->translatedFormat('l, d F Y');
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function view($invoice)
    {
        $order = Order::with(['district.city.province', 'details', 'details.product', 'payment'])
            ->where('invoice', $invoice)->first();

        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.orders.view', compact('order'));
            }
        }else {
            return redirect()->back();
        }

        return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Order Orang Lain']);
    }

    public function paymentForm($invoice)
    {
        $order = Order::with(['payment'])->where('invoice', $invoice)->first();

        // get customer
        $customer = Customer::where('id', $order->customer_id)->first();

        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.payment', compact('order', 'customer'));
            }
        }else {
            return redirect()->back();
        }

        return redirect()->back()->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Payment Order Orang Lain']);
    }

    public function storePayment(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'invoice' => 'required|exists:orders,invoice',
            'name' => 'required|string',
            'transfer_to' => 'required|string',
            'transfer_date' => 'required',
            'amount1' => 'required',
            'proof' => 'required|image|mimes:jpg,png,jpeg'
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        //DEFINE DATABASE TRANSACTION UNTUK MENGHINDARI KESALAHAN SINKRONISASI DATA JIKA TERJADI ERROR DITENGAH PROSES QUERY
        DB::beginTransaction();
        try {
            $order = Order::where('invoice', $request->invoice)->first();

            if ($order->total != $request->amount) {
                // return redirect()->back()->with(['error' => 'Error, Pembayaran Harus Sama Dengan Tagihan']);
                return response()->json(['error' => 'Error, Pembayaran Harus Sama Dengan Tagihan'], 400);
            }

            if ($order->status == 0 && $request->hasFile('proof')) {
                $file = $request->file('proof');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/proof', $filename);

                Payment::create([
                    'order_id' => $order->id,
                    'name' => $request->name,
                    'transfer_to' => $request->transfer_to,
                    'transfer_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'amount' => $request->amount,
                    'proof' => $filename,
                    'status' => false
                ]);

                $order->update(['status' => 1]);
                DB::commit();
                // return redirect()->route('customer.view_order', $order->invoice)->with(['success' => 'Pesanan Dikonfirmasi']);

                return response()->json(['success' => 'Pembayaran berhasil, Pesanan dikonfirmasi', 'redirect' => route('customer.view_order', $order->invoice)], 200);
            }

            // return redirect()->back()->with(['error' => 'Error, Upload Bukti Transfer']);

            return response()->json(['error' => 'Error, Upload Bukti Transfer'], 400);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function pdf($invoice)
    {
        try {
            $order = Order::with(['district.city.province', 'details', 'details.product', 'payment'])
                ->where('invoice', $invoice)
                ->first();

            if (!$order) {
                return response()->json(['error' => 'File PDF tidak ditemukan'], 404);
            }

            if (!\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
                return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Invoice Orang Lain']);
            }

            $baseName = $order->invoice . '-invoice.pdf';
            $folderPath = 'public/docs/members/invoice/';
            $storagePath = $folderPath . $baseName;

            // Cek dan ubah nama jika file sudah ada
            $i = 1;
            while (Storage::exists($storagePath)) {
                $baseName = $order->invoice . '-invoice (' . $i . ').pdf';
                $storagePath = $folderPath . $baseName;
                $i++;
            }

            // Simpan file di storage
            $pdf = PDF::loadView('ecommerce.orders.pdf', compact('order'));
            Storage::put($storagePath, $pdf->output());

            if(request()->ajax()) {
                return response()->json(['success' => 'PDF berhasil diunduh'], 200);
            }

            // Download ke browser
            return response()->download(storage_path('app/' . $storagePath), $baseName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function acceptOrder(Request $request)
    {
        try {
            // Validasi awal
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id'
            ]);

            $order = Order::find($validated['order_id']);

            if (!$order) {
                return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
            }

            // Cek otorisasi
            if (!\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
                return response()->json(['error' => 'Bukan pesanan kamu'], 403);
            }

            // Cek status
            if ($order->status != 3) {
                return response()->json(['error' => 'Pesanan tidak dalam status "dikirim"'], 400);
            }

            // Update status ke '4' = Sampai
            $order->update(['status' => 4]);

            return response()->json(['success' => 'Pesanan berhasil dikonfirmasi sebagai diterima','order_status' => 4]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function returnForm($invoice)
    {
        $order = Order::where('invoice', $invoice)->first();
        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.orders.return', compact('order'));
            }
        }else {
            return redirect()->back();
        }

        return redirect()->back()->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Return Order Orang Lain']);
    }

    public function processReturn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string',
                'refund_transfer' => 'required|string',
                'photo' => 'required|image|mimes:jpg,png,jpeg'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }

            $order = Order::find($request->order_id);
            $return = OrderReturn::where('order_id', $request->order_id)->first();
            if ($return) {
                return redirect()->back()->with(['error' => 'Permintaan Refund Dalam Proses']);
            }

            if($request->refund_transfer != $order->subtotal){
                return response()->json(['error' => 'Nominal yang dimasukkan tidak sesuai dengan subtotal'], 400);
            }

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/returns', $filename);

                OrderReturn::create([
                    'order_id' => $request->order_id,
                    'photo' => $filename,
                    'reason' => $request->reason,
                    'refund_transfer' => $request->refund_transfer,
                    'status' => 0
                ]);

                //kirim pesan return
                // $this->sendMessage($order->invoice, $request->reason);

                // return redirect()->route('customer.orders')->with(['success' => 'Permintaan Refund Dikirim']);
                return response()->json(['success' => 'Berhasil mengirim permintaan refund', 'redirect' => route('customer.orders')], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    //Curl Telegram
    private function getTelegram($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $params);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $content = curl_exec($ch);
        curl_close($ch);
        return json_decode($content, true);
    }

    private function sendMessage($invoice, $reason)
    {
        $key = env('TELEGRAM_KEY');

        $chat = $this->getTelegram('https://api.telegram.org/'. $key .'/getUpdates', '');

        if ($chat['ok']) {
            //cukup ambil key 0 atau admin saja untuk mendapatkan chat_id
            $chat_id = $chat['result'][0]['message']['chat']['id'];

            $text = 'Hai Admin E-Commerce, OrderID '.$invoice.' Melakukan Permintaan Refund Dengan Alasan "'. $reason.'", Silahkan Segera Dicek Ya!';

            //kirim request ke telegram untuk mengirim pesan
            return $this->getTelegram('https://api.telegram.org/'. $key .'/sendMessage', '?chat_id=' . $chat_id . '&text=' . $text);
        }
    }

}
