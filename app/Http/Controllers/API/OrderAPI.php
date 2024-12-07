<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Cart;

class OrderAPI extends Controller
{
    public function place_order(Request $request){
        $validator = Validator::make($request->all(), [
            'order_type' => 'required|in:delivery,dine-in',
            'coupone_code' => 'nullable|string',
            'formatted_address' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'contact_number' => 'nullable',
            'contact_purson' => 'nullable',
            'delevery_note' => 'nullable',
            'payment_method' => 'required|in:Cash On Delevery,Online'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $cart_items = Cart::where('user_id', $request->user()->id)->get();
        if($cart_items){

            $cart_sub_total = calculate_cart_sub_total_by_userId($request->user()->id);
            $cart_total = calculate_cart_total_by_userId($request->user()->id);
            $coupone_discount = !empty($request->coupone_code) ? get_coupone_discount($request->coupone_code,$cart_total) : 0.00;

            $order = new Order();
            $order->order_number = generateOrderNumber();
            $order->user_id = $request->user()->id;
            $order->order_type = $request->order_type;
            $order->coupone_code = $request->coupone_code;
            $order->coupone_discount = $coupone_discount;
            $order->price_subtotal = $cart_sub_total;
            $order->price_gst = 0.00;
            $order->price_shipping = 0.00;
            $order->total_amount = calculate_cart_total_by_userId($request->user()->id) - $coupone_discount;
            $order->discounted_price = $cart_sub_total-$order->total_amount;
            $order->payment_method = $request->payment_method;
            $order->payment_status = $request->payment_method == 'Online' ? 'Payment Received':'Awaiting Payment';
            $order->formatted_address = $request->formatted_address;
            $order->latitude = $request->latitude;
            $order->longitude = $request->longitude;
            $order->longitude = $request->longitude;
            $order->contact_number = $request->contact_number;
            $order->contact_purson = $request->contact_purson;
            $order->delevery_note = $request->delevery_note;
            $order->save();

            update_order_number($order->id, $order->order_number);

            foreach($cart_items as $cart_item){
                // return $cart_item->product->name;
                $order_item = new OrderItems();
                $order_item->order_id = $order->id;
                $order_item->product_id = $cart_item->product_id;
                $order_item->product_name = $cart_item->product->name;
                $order_item->quantity = $cart_item->quantity;
                $order_item->price = $cart_item->product->total_price;
                $order_item->subtotal = $cart_item->product->total_price * $cart_item->quantity;
                $order_item->save();
            }

            //clear cart
            $cart_items = Cart::where('user_id', $request->user()->id)->delete(); 

            return response()->json([
                'status' => 'true',
                'message' => 'Order Created Successfully',
                'data' => $order
            ], 200);
        }else{
            return response()->json([
                'status' => 'false',
                'message' => 'Empty Cart'
            ], 200);
        }
    }

    public function order_history(){
        $orders = Order::with('items.product.media')->orderBy('id','desc')->get();
        return response()->json([
            'status' => 'true',
            'data' => $orders
        ], 200);
    }

    public function order_details($id = null){
        if($id != null){
            $order = Order::with('items.product.media')->where('id',$id)->get();
            return response()->json([
                'status' => 'true',
                'data' => $order
            ], 200);
        }else{
            return response()->json([
                'status' => 'false',
                'maessage' => 'Please provide Order ID'
            ], 200);
        }

    }

    public function cancel_order(Request $request){
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'cause' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $order = Order::find($request->order_id);
        $order->order_status = 'Cancelled';
        $order->cancel_cause = $request->cause;
        $res = $order->update();
        if($res){
            return response()->json([
                'status' => 'true',
                'message' => 'Order Cancelled Successfully',
                'data' => $order
            ], 200);
        }else{
            return response()->json([
                'status' => 'false',
                'message' => 'Order Not Cancelled',
                'data' => $order
            ], 200);
        }
    }
}