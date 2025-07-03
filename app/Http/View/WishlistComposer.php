<?php

namespace App\Http\View;

use Illuminate\View\View;
use App\Wishlist;
use App\Product;
use Carbon\Carbon;

class WishlistComposer {
    
    private function getWishlist() {
        if(auth()->guard('customer')->check()){
            // Fetch wishlist items with product information in one query
            $wishlists = Wishlist::with(['product'])->where('customer_id', auth()->guard('customer')->user()->id)->orderBy('created_at', 'DESC')->paginate(10);

            return $wishlists;
        }
    }

    private function getTotalWishlistCount() {
        if(auth()->guard('customer')->check()){
            $userId = auth()->guard('customer')->user()->id;

            if (!$userId) {
                return 0;
            }

            return Wishlist::where('customer_id', $userId)->count();
        }
    }

    public function compose(View $view)
    {
        $getWishlist = $this->getWishlist();
        $totalWishlistCount = $this->getTotalWishlistCount();

        $view->with('getWishlist', $getWishlist)
             ->with('totalWishlistCount', $totalWishlistCount);
    }
}