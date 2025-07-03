<?php

namespace App\Http\View;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Order;

class OrderComposer
{
    private function getOrdersWithSpecificStatusesCount()
    {
        // get customer id
        $customerIds = auth()->guard('customer')->user()?->id;

        if (Auth::guard('customer')->check()) {
            // return Order::where('customer_id', Auth::guard('customer')->user()->id)
            //     ->whereHas('details', function($query) {
            //         $query->whereIn('status', [1, 2, 3, 4, 5]);
            //     })->count();
            
            return Order::where('customer_id', $customerIds)->whereIn('status', [0, 1, 2, 3])->whereHas('return')->count();  
        }

        return 0;
    }

    private function getProductStatusAnnouncements()
    {
        try {
            $customer = Auth::guard('customer')->user();
            if (!$customer) return collect(); // agar selalu kembalikan Collection

            $statusDescriptions = [
                0 => 'Pesanan baru, harap segera melakukan pembayaran',
                1 => 'Pesanan dikonfirmasi',
                2 => 'Pesanan diproses',
                3 => 'Pesanan dikirim'
            ];

            $statusReturnDescriptions = [
                0 => 'Produk ini sedang mengajukan return',
                1 => 'Return sudah dikonfirmasi',
                2 => 'Return batal dikonfirmasi'
            ];

            $orders = Order::with([
                    'details.product:id,name,image',
                    'return' => function ($q) {
                        $q->select('order_id', 'status')->whereIn('status', [0, 1, 2]); // Hanya ambil return yang sedang dalam proses atau sudah dikonfirmasi
                    }
                ])
                ->where('customer_id', $customer->id)
                ->whereIn('status', [0, 1, 2, 3])
                ->whereHas('details.product')
                ->whereHas('return', function ($query) {
                    $query->select('status')->whereIn('status', [0, 1, 2]); // Hanya ambil return yang sedang dalam proses atau sudah dikonfirmasi
                })
                ->orderByDesc('created_at')
                ->get(['id', 'invoice', 'status', 'subtotal', 'cost']);

            $announcements = [];

            foreach ($orders as $order) {
                foreach ($order->details as $detail) {
                    if (!$detail->product) continue;

                    $returnStatus = optional($order->return->first())->status;

                    $announcements[] = [
                        'product_name' => $detail->product->name,
                        'image' => $detail->product->image,
                        'qty' => $detail->qty,
                        'price' => $detail->price,
                        'status' => $statusDescriptions[$order->status] ?? 'Status tidak diketahui',
                        'status_count' => $order->status,
                        'total' => 'Rp ' . number_format($order->subtotal + $order->cost, 0, ',', '.'),
                        'invoice' => $order->invoice,
                        'return_status' => $returnStatus !== null && isset($statusReturnDescriptions[$returnStatus])
                            ? $statusReturnDescriptions[$returnStatus]
                            : null,
                    ];
                }
            }

            return collect($announcements); 

        } catch (\Exception $e) {
            Log::error('Error fetching product status announcements: ' . $e->getMessage());
            return collect(); // Kembalikan Collection kosong jika error
        }
    }

    public function compose(View $view)
    {
        $ordersWithSpecificStatusesCount = $this->getOrdersWithSpecificStatusesCount();
        $productStatusAnnouncements = $this->getProductStatusAnnouncements();

        $view->with('ordersWithSpecificStatusesCount', $ordersWithSpecificStatusesCount)
             ->with('productStatusAnnouncements', $productStatusAnnouncements);
    }
}