<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Cart;

class CartAPI extends Controller
{
    public function add_to_cart(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $existingCartItem = Cart::where('user_id', $request->user()->id)
        ->where('product_id', $request->product_id)
        ->first();

        if ($existingCartItem) {
            // Update quantity if the product is already in the cart
            $existingCartItem->quantity += $request->quantity;
            $existingCartItem->save();

            $existingCartItem->load('product');
            $existingCartItem->product->image_url = getProductMainImage($existingCartItem->product_id);

            return response()->json([
                'status' => 'true',
                'message' => 'Cart updated successfully.',
                'data' => $existingCartItem,
            ], 200);
        }

        // Create a new cart item
        $cartItem = Cart::create([
            'user_id' => $request->user()->id, // Ensure the user is authenticated
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        $cartItem->load('product');

        $cartItem->product->image_url = getProductMainImage($cartItem->product_id);

        return response()->json([
            'status' => 'true',
            'message' => 'Product added to cart successfully.',
            'data' => $cartItem,
        ], 201);
    }

    public function cart_items(Request $request){
        $cart_items = Cart::with('product')->where('user_id', $request->user()->id)->get();

        $cart_items->each(function ($cartItem) {
            // Load the media collection for each product
            $cartItem->product->image_url = getProductMainImage($cartItem->product_id);
        });

        return response()->json([
            'status' => 'true',
            'cart_total' => calculate_cart_total_by_userId($request->user()->id),
            'data' => $cart_items,
        ], 200);
    }

    public function increment_decrement_cart_quantity(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:carts,product_id',
            'increment_or_decrement' => 'required|in:increment,decrement',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $existingCartItem = Cart::where('user_id', $request->user()->id)
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($existingCartItem) {
            if($request->increment_or_decrement == 'increment'){
                $existingCartItem->quantity += $request->quantity;
            }
            if($request->increment_or_decrement == 'decrement'){
                $existingCartItem->quantity -= $request->quantity;
            }
            $existingCartItem->save();

            $existingCartItem->load('product');
            $existingCartItem->product->image_url = getProductMainImage($existingCartItem->product_id);

            return response()->json([
                'status' => 'true',
                'message' => 'Cart Item updated successfully.',
                'data' => $existingCartItem,
            ], 200);
        }
    }

    public function delete_cart_item(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:carts,product_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $existingCartItem = Cart::where('user_id', $request->user()->id)
        ->where('product_id', $request->product_id)
        ->first();

        if ($existingCartItem) {
            $existingCartItem->delete();
            
            $CartItems = Cart::with('product')->where('user_id', $request->user()->id)->get();

            $CartItems->each(function ($cartItem) {
                // Load the media collection for each product
                $cartItem->product->image_url = getProductMainImage($cartItem->product_id);
            });

            return response()->json([
                'status' => 'true',
                'message' => 'Item deleted successfully.',
                'data' => $CartItems
            ], 200);
        }else{
            return response()->json([
                'status' => 'false',
                'message' => 'Cart Item Not Exists.'
            ], 200);
        }
    }

    public function clear_cart(Request $request){
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'status' => 'true',
            'message' => 'Cart Cleared successfully.',
        ], 200);
    }
}