<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('id','desc')->get();
        return view('admin.orders.index',compact('orders'));
    }

    public function show($id){
        $order = Order::find($id);
        $buyer_details = User::find($order->user_id);
        $order_items = $order->items;
        return view('admin.orders.show',compact('order','buyer_details','order_items'));
    }

    public function update_order_status(Request $request){
        $order = Order::find($request->order_id);
        if($request->order_status == 'Rejected'){
            $order->cancel_cause = $request->cancel_cause;
            // $order->is_cancel = 1;
            
            if($order->order_status == 'Order Placed' || $order->order_status == 'Order Confirmed'){
                $order->cancel_cause = $request->cancel_cause;
                // $order->is_cancel = 1;
            }else{
                return redirect()->back()->with(['error'=>'Order is now being '.ucfirst($order->order_status).', and it cannot be reject or cancel at this time.']);
            }
        }elseif($request->order_status == 'Delivered'){
            $order->status = 1;
        }
        // if($r->order_status == 'Ready for Pickup' && $order->order_type != 'takeaway'){
        //     if($r->delivary_partner){
        //         $delivery = new Delivery();
        //         $delivery->order_id = $order->id;
        //         // $delivery->order_type = 'delivery';
        //         $delivery->order_type = 'order';
        //         $delivery->partner_id = $r->delivary_partner;
        //         $delivery->status = "Delivery Assigned";
        //         $delivery->is_delivered = 0;
        //         $res = $delivery->save();
        //         if($res){
        //             $device_tokens = get_device_token_by_user_id($r->delivary_partner);
        //             if(!empty($device_tokens)){
        //                 $data = massage_data($delivery->status);
        //                 sendFcmNotification($device_tokens, $delivery->status, $delivery->status);
        //             }
        //         }
        //     }else{
        //         return redirect()->back()->with('error','Delivery Pertner is Required');
        //     }
        // }
        $order->order_status = $request->order_status;
        $result = $order->update();

        // if($result){
        //     $device_tokens = get_device_token_by_order_id($order->id);
        //     if(!empty($device_tokens)){
        //         $data = massage_data($r->order_status);
        //         sendFcmNotification($device_tokens, $r->order_status, $data);
        //     }
        // }
        return redirect()->back()->with(['success'=>'Status Updated Successfully']);
    }

    public function update_payment_status(Request $request){
        $order = Order::find($request->order_id);
        $order->payment_status = $request->order_status;
        // if($r->order_status == 'paid'){
        //     $order->payment_date = now();
        // }
        $order->update();
        return redirect()->back()->with(['success'=>'Payment Updated Successfully']);
    }

    public function destroy($id){
        $order = Order::find($id);
        if($order){
            $res = $order->delete();
            if($res){
                return back()->with('success','Deleted Successfully');
            }else{
                return back()->with('error','Not Deleted');
            }
        }else{
            return back()->with('error','Not Found');
        }
    }
}