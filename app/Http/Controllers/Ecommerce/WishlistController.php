<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Wishlist;
use DataTables;
use DB;

class WishlistController extends Controller
{
    public function index()
    {
        return view('ecommerce.wishlists.index');
    }

    public function getWishlistDatatables(Request $request)
    {
        // customer id
        $customerIds = auth()->guard('customer')->user()?->id;

        $wishlists = Wishlist::with(['product'])->where('customer_id', $customerIds)->orderBy('created_at', 'DESC')->get();

        return DataTables::of($wishlists)
            ->addColumn('action', function ($wishlist) {
                return '
                    <a href="' . url('/product/' . $wishlist->product->slug) . '" class="btn btn-sm btn-primary mr-1" title="Lihat Produk ' . $wishlist->product->name . '">
                        <span class="fas fa-eye"></span>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger delete-wishlist" data-wishlist-id="'. $wishlist->id .'" title="Hapus Produk ' . $wishlist->product->name . '"><span class="fa fa-trash"></span></button>
 
                    <form id="deleteForm{{ $wishlist->product_id }}" action="'. route('customer.deleteWishlist', $wishlist->product_id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->addColumn('image', function ($wishlist) {
                return '<img src="' . asset('/storage/products/' . $wishlist->product->image) . '" alt="' . $wishlist->product->name . '" class="img-thumbnail rounded" style="width: 110px; height: 100px; object-fit: contain; display: block;">';
            })
            ->editColumn('name', function ($wishlist) {
                return $wishlist->product->name;
            })
            ->editColumn('amount', function ($wishlist) {
                return 'Rp. ' . number_format($wishlist->product->price, 0, ',', '.');
            })
            ->rawColumns(['action', 'status', 'image'])
            ->make(true);
    }

    public function saveWishlist(Request $request)
    {
        try {
            $this->validate($request, [
                'product_id' => 'required|exists:products,id'
            ]);

            Wishlist::create([
                'customer_id' => auth()->guard('customer')->user()->id,
                'product_id' => $request->product_id
            ]);

            // return redirect()->back()->with(['success' => 'Produk ditambahkan ke Wishlist']);
            return response()->json(['success' => 'Produk ditambahkan ke daftar keinginan'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function deleteWishlist($id)
    {
        $wishlist = Wishlist::find($id);
        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => 'Produk berhasil dihapus dari daftar keinginan'], 200);
        }

        return response()->json(['error' => 'Produk di Daftar Keinginan ini Tidak Ada'], 404);
    }
        
}
