<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FlashSaleController extends Controller
{
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_uuid' => 'required|string|size:36',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Unprocessable Entity',
                'messages' => $validator->errors()
            ], 422);
        }

        $productUuid = $request->input('product_uuid');
        $quantity = $request->input('quantity');

        try {
            return DB::transaction(function () use ($productUuid, $quantity) {
                $product = Product::where('products_uuid', $productUuid)->first();

                if (!$product) {
                    return response()->json(['error' => 'Product not found'], 404);
                }

                $inventory = Inventory::where('inventories_products_uuid', $productUuid)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    return response()->json(['error' => 'Inventory not found'], 404);
                }

                if ($inventory->inventories_quantity < $quantity) {
                    return response()->json(['error' => 'Insufficient stock'], 400);
                }

                $inventory->inventories_quantity -= $quantity;
                $inventory->save();

                $order = Order::create([
                    'orders_transaction_status' => 'success',
                    'orders_status' => 'active',
                    'orders_total_amount' => $product->products_flashsale_price * $quantity,
                ]);

                $orderItem = OrderItem::create([
                    'order_items_orders_uuid' => $order->orders_uuid,
                    'order_items_products_uuid' => $productUuid,
                    'order_items_quantity' => $quantity,
                    'order_items_price' => $product->products_flashsale_price,
                    'order_items_status' => 'active',
                ]);

                return response()->json([
                    'message' => 'Purchase successful',
                    'order' => $order,
                    'order_item' => $orderItem,
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
