<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\OrderDetail;
use App\OrderReturn;
use App\Payment;
use App\Product;
use App\Customer;

use App\District;
use App\City;
use App\Province;

use App\Mail\OrderMail;
use Mail;
use Carbon\Carbon;
use PDF;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index');
    }

    public function datatables(Request $request)
    {
        $orders = Order::with(['customer.district.city.province'])->withCount('return')->orderBy('created_at', 'DESC');

        return DataTables::of($orders)
                ->editColumn('dates', function($order) {
                    return Carbon::parse($order->created_at)->locale('id')->translatedFormat('l, d F Y');
                })
                ->editColumn('invoice', function($order) {
                    return $order->invoice;
                })
                ->editColumn('customer_name', function($order){
                    return $order->customer_name;
                })
                ->editColumn('total', function($order){
                    return 'Rp ' . number_format($order->total, 0, ',', '.');
                })
                ->addColumn('action', function ($order) use (&$index) {
                    static $index = 0;
                    $index++;
                    return '
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary view-order" data-index="'.$index.'" data-invoice="' . $order->invoice . '" title="Detail Invoice '. $order->invoice . '">
                            <span class="fa fa-eye"></span>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger ml-1 delete-order" title="Hapus Invoice '. $order->invoice . '" data-order-id="' . $order->id . '"><span class="fa fa-trash"></span></button>

                        <form id="deleteForm' . $order->id . '" action="' . route('orders.newDestroy', $order->id) . '" method="post" class="d-none">
                            ' . method_field('DELETE') . csrf_field() . '
                        </form>
                    ';
                })
                ->rawColumns(['details', 'action', 'totalProduct', 'formattedDate'])
                ->make(true);
    }

    public function view($invoice)
    {
        if (Order::where('invoice', $invoice)->exists()){
            $order = Order::with(['customer.district.city.province', 'return', 'payment', 'details.product'])->withCount('return')->where('invoice', $invoice)->first();
            return view('orders.view', compact('order'));
        }else {
            return redirect()->back();
        }
    }

    public function acceptPayment($invoice)
    {
        try {
            $order = Order::with(['payment'])->where('invoice', $invoice)->first();

            // generate unik id untuk tracking number
            $uuid = (string) Str::uuid();
            $trackingNumber = 'TRX-' . strtoupper(substr($uuid, 0, 8));

            $order->payment()->update(['status' => 1]);
            $order->update([
                'status' => 2,
                'tracking_number' => $trackingNumber,
            ]);
            // return redirect(route('orders.newView', $order->invoice))->with(['success' => 'Pembayaran Sudah dikonfirmasi']);

            return response()->json(['success' => 'Pembayaran berhasil dikonfirmasi'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function shippingOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tracking_number' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }

            $order = Order::with(['customer'])->find($request->order_id);

            if(!$order){
                return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
            }

            $order->update(['tracking_number' => $request->tracking_number, 'status' => 3]);

            // Mail::to($order->customer->email)->send(new OrderMail($order));
            // return redirect()->back()->with('success', 'Data berhasil dikirim!');
            return response()->json(['success' => 'Pesanan berhasil dikirim'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function return($invoice)
    {
        if (Order::where('invoice', $invoice)->exists()){
            $order = Order::with(['return', 'customer'])->where('invoice', $invoice)->first();
            return view('orders.return', compact('order'));
        }else {
            return redirect()->back();
        }
    }

    public function approveReturn(Request $request)
    {
        try {
            // $this->validate($request, []);
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }

            $order = Order::find($request->order_id);
            $order->return()->update(['status' => $request->status]);
            $order->update(['status' => 4]);
            return response()->json(['success' => 'Berhasil memproses permintaan refund', 'redirect' => route('orders.newView', $order->invoice)], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function orderReport()
    {
        return view('report.index');
    }

    public function getOrderReportDatatables(Request $request)
    {
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');


        $orders = Order::with(['customer.district'])->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC')->get();

        return DataTables::of($orders)
            ->editColumn('dates', function($order) {
                return Carbon::parse($order->created_at)->locale('id')->translatedFormat('l, d F Y');
            })
            ->editColumn('invoice', function($order) {
                return $order->invoice;
            })
            ->addColumn('details', function($order){
                return '
                    <div class="d-flex flex-column">
                        <p class="custom-margin font-weight-bold">
                            <span>' . $order->customer_name . ' (' . $order->customer->phone_number . ')</span>
                        </p>
                        <p class="custom-margin">' . $order->customer->email . '</p>
                        <p class="custom-margin text-justify" style="white-space: pre-line;">' . $order->customer_address . ', Kecamatan ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name . ', ' . $order->customer->district->city->province->name . ', Kode Pos ' . $order->customer->district->city->postal_code . ', Indonesia</p>
                    </div>
                ';
            })
            ->editColumn('total', function($order){
                return 'Rp ' . number_format($order->total, 0, ',', '.');
            })
            ->rawColumns(['details'])
            ->make(true);
    }

    public function orderReportPdf($daterange)
    {
        try {
            $date = explode('+', $daterange);

            $start = Carbon::parse($date[0])->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::parse($date[1])->endOfDay()->format('Y-m-d H:i:s');

            $orders = Order::with(['customer.district'])->whereBetween('created_at', [$start, $end])->get();
            $startFormatted = Carbon::parse($start)->locale('id')->translatedFormat('l, d F Y');
            $endFormatted = Carbon::parse($end)->locale('id')->translatedFormat('l, d F Y');
            $fileName = "Laporan Pesanan Periode {$startFormatted} - {$endFormatted}.pdf";

            $pdf = PDF::loadView('report.orderpdf', compact('orders', 'date'));

            $storagePath = 'public/docs/reports/reports/';
            $filePath = $storagePath . $fileName;
            $i = 1;
            while (Storage::exists($filePath)) {
                $fileName = "Laporan Pesanan Periode {$startFormatted} - {$endFormatted} ({$i}).pdf";
                $filePath = $storagePath . $fileName;
                $i++;
            }

            Storage::put($filePath, $pdf->output());
            if (request()->ajax()) {
                return response()->json(['success' => 'PDF berhasil dibuat', 'file_url' => asset('storage/docs/reports/reports/' . $fileName)], 200);
            }

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    public function returnReport()
    {
        return view('report.return');
    }

    public function returnReportDatatables(Request $request)
    {
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        $orders = Order::with(['customer.district'])->has('return')->whereBetween('created_at', [$start, $end])->get();

        return DataTables::of($orders)
            ->editColumn('invoice', function($order) {
                return $order->invoice;
            })
            ->addColumn('details', function($order){
                $returns = optional($order->return->first())->status_label ?? '-';
                return '
                    <div class="d-flex flex-column">
                        <p class="custom-margin font-weight-bold">
                            <span>Status : ' . $returns . '</span>
                        </p>
                        <p class="custom-margin font-weight-bold">
                            <span>' . $order->customer_name . ' (' . $order->customer->phone_number . ')</span>
                        </p>
                        <p class="custom-margin">' . $order->customer->email . '</p>
                        <p class="custom-margin text-justify" style="white-space: pre-line;">' . $order->customer_address . ', Kecamatan ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name . ', ' . $order->customer->district->city->province->name . ', Kode Pos ' . $order->customer->district->city->postal_code . ', Indonesia</p>
                    </div>
                ';
            })
            ->addColumn('reason', function($order){
                return optional($order->return->first())->reason ?? '-';
            })
            ->editColumn('dates', function($order) {
                return optional($order->return->first())->created_at
                    ? Carbon::parse($order->return->first()->created_at)->locale('id')->translatedFormat('l, d F Y')
                    : '-';
            })
            ->addColumn('refundTransfer', function($order){
                $amount = optional($order->return->first())->refund_transfer ?? '0';
                return 'Rp ' . number_format($amount, 0, ',', '.');
            })
            ->rawColumns(['details', 'reason', 'refundTransfer'])
            ->make(true);
    }

    public function returnReportPdf($daterange)
    {
        try {
            $date = explode('+', $daterange);

            $start = Carbon::parse($date[0])->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::parse($date[1])->endOfDay()->format('Y-m-d H:i:s');

            $orders = Order::with(['customer.district'])->has('return')->whereBetween('created_at', [$start, $end])->get();

            $startFormatted = Carbon::parse($start)->locale('id')->translatedFormat('l, d F Y');
            $endFormatted = Carbon::parse($end)->locale('id')->translatedFormat('l, d F Y');
            $fileName = "Laporan Pengembalian Pesanan Periode {$startFormatted} - {$endFormatted}.pdf";

            $pdf = PDF::loadView('report.returnpdf', compact('orders', 'date'));

            $storagePath = 'public/docs/reports/returns/';
            $filePath = $storagePath . $fileName;
            $i = 1;
            while (Storage::exists($filePath)) {
                $fileName = "Laporan Pengembalian Pesanan Periode {$startFormatted} - {$endFormatted} ({$i}).pdf";
                $filePath = $storagePath . $fileName;
                $i++;
            }

            Storage::put($filePath, $pdf->output());
            if (request()->ajax()) {
                return response()->json(['success' => 'PDF berhasil dibuat', 'file_url' => asset('storage/docs/reports/returns/' . $fileName)], 200);
            }

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::find($id);
            if(!$order){
                return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
            }

            $order->details()->delete();
            $order->payment()->delete();
            $order->delete();
            return response()->json(['success' => 'Pesanan berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }
}
