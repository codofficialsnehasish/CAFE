<?php
    use App\Models\Product;
    use App\Models\Cart;
    use App\Models\Coupon;

    if(!function_exists('getProductMainImage')){
        function getProductMainImage($productId){
            $product = Product::find($productId);

            if (!$product) {
                return null;
            }
            $mainImage = $product->getMedia('products-media')
                ->firstWhere('custom_properties.is_main', true);

            return $mainImage ? $mainImage->getUrl() : null;
        }
    }

    if(!function_exists('calculate_cart_total_by_userId')){
        function calculate_cart_total_by_userId(int $userId)
        {
            $total = 0;

            $cartItems = Cart::where('user_id', $userId)->get();

            foreach ($cartItems as $cartItem) {
                $total += $cartItem->quantity * $cartItem->product->total_price;
            }

            return $total;
        }
    }

    if(!function_exists('calculate_cart_sub_total_by_userId')){
        function calculate_cart_sub_total_by_userId(int $userId)
        {
            $total = 0;

            $cartItems = Cart::where('user_id', $userId)->get();

            foreach ($cartItems as $cartItem) {
                $total += $cartItem->quantity * $cartItem->product->price;
            }

            return $total;
        }
    }