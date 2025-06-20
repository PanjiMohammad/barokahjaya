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
        $order = Order::with(['payment'])->where('invoice', $invoice)->first();

        $order->payment()->update(['status' => 1]);
        $order->update(['status' => 2]);
        return redirect(route('orders.newView', $order->invoice))->with(['success' => 'Pembayaran Sudah dikonfirmasi']);
    }

    public function shippingOrder(Request $request)
    {
        $order = Order::with(['customer'])->find($request->order_id);
        $order->update(['tracking_number' => $request->tracking_number, 'status' => 3]);

        // Mail::to($order->customer->email)->send(new OrderMail($order));
        return redirect()->back()->with('success', 'Data berhasil dikirim!');
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
        $this->validate($request, ['status' => 'required']);

        $order = Order::find($request->order_id);
        $order->return()->update(['status' => $request->status]);
        $order->update(['status' => 4]);
        return redirect()->back();
    }

    public function orderReport()
    {
        $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        if (request()->date != '') {
            $date = explode(' - ' ,request()->date);
            $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';
        }
    
        $orders = Order::with(['customer.district'])->whereBetween('created_at', [$start, $end])->get();

        return view('report.index', compact('orders'));
    }

    public function orderReportPdf($daterange)
    {
        $date = explode('+', $daterange); 

        $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

        $orders = Order::with(['customer.district'])->whereBetween('created_at', [$start, $end])->get();
        $pdf = PDF::loadView('report.orderpdf', compact('orders', 'date'));

        $startpdf = Carbon::parse($date[0])->format('d-F-Y');
        $endpdf = Carbon::parse($date[1])->format('d-F-Y');
        return $pdf->download('Laporan Order '.$startpdf.' sampai '.$endpdf.'.pdf');
    }

    public function returnReport()
    {
        $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        if (request()->date != '') {
            $date = explode(' - ' ,request()->date);
            $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';
        }

        $orders = Order::with(['customer.district'])->has('return')->whereBetween('created_at', [$start, $end])->get();
        return view('report.return', compact('orders'));
    }

    public function returnReportPdf($daterange)
    {
        $date = explode('+', $daterange);
        $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

        $orders = Order::with(['customer.district'])->has('return')->whereBetween('created_at', [$start, $end])->get();
        $pdf = PDF::loadView('report.returnpdf', compact('orders', 'date'));
        
        $startpdf = Carbon::parse($date[0])->format('d-F-Y');
        $endpdf = Carbon::parse($date[1])->format('d-F-Y');
        return $pdf->download('Laporan Return Order '.$startpdf.' sampai '.$endpdf.'.pdf');
    }

    public function destroy($id)
    {
        try {
            $order = Order::find($id);
            $order->details()->delete();
            $order->payment()->delete();
            $order->delete();
            return response()->json(['success' => 'Order berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }
}
