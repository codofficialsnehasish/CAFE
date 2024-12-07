<?php

    use App\Models\Order;
    use App\Models\OrderItems;
    use App\Models\Cart;
    use App\Models\Coupon;

    if (!function_exists('generateOrderNumber')) {
        function generateOrderNumber() {
            $dateTime = date('YmdHis');
            $orderNumber = 'ORD' . $dateTime;
            return $orderNumber;
        }
    }

    if (!function_exists('update_order_number')) {
        function update_order_number($order_id, $order_number)
        {
            $data = array(
                'order_number' => $order_number.$order_id
            );
            Order::where('id', $order_id)->update($data);
        }
    }


    if (!function_exists('get_coupone_discount')) {
        function get_coupone_discount($coupone_code,$amount)
        {
            // Fetch the coupon based on the code
            $coupone = Coupon::where('code', $coupone_code)
                            ->where('is_active', 1)
                            ->whereDate('start_date', '<=', now())
                            ->whereDate('end_date', '>=', now())
                            ->first();

            // If the coupon is not found or is inactive/expired
            if (!$coupone) {
                return 0.00; // No discount
            }

            // Check minimum purchase amount
            if ($amount < $coupone->minimum_purchase) {
                return 0.00; // No discount if purchase amount is less than required
            }

            // Calculate discount based on coupon type
            if ($coupone->type === 'percentage') {
                $discount = ($coupone->value / 100) * $amount; // Percentage discount
            } elseif ($coupone->type === 'flat') {
                $discount = $coupone->value; // Flat discount
            } else {
                $discount = 0.00; // Fallback for unknown types
            }

            // Ensure the discount does not exceed the total amount
            return min($discount, $amount);
        }
    }