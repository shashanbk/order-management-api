<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    
    // 1. Get User Orders
    public function index() {
        $orders = auth()->user()->orders()->with('items.product')->latest()->get();
        return OrderResource::collection($orders)->additional(['message' => 'Orders retrieved successfully']);
    }

    // 2. Place Order
    public function store(Request $request) {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // 1. Run the Transaction and capture the returned $order object
            $order = DB::transaction(function () use ($request) {
                $totalPrice = 0;
                $orderItemsData = [];

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    // Check Stock
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock unavailable for: {$product->name}");
                    }

                    $totalPrice += ($product->price * $item['quantity']); 
                    $product->decrement('stock', $item['quantity']); 

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                    ];
                }

                $newOrder = auth()->user()->orders()->create([
                    'total_price' => $totalPrice,
                    'status' => 'completed'
                ]);

                $newOrder->items()->createMany($orderItemsData);
                
                return $newOrder; // Return the created order to the variable outside
            });

            // 2. SEND THE EMAIL (Now that the DB transaction is committed)
            Mail::to($request->user())->send(new OrderPlaced($order));

            // 3. RETURN THE FINAL RESPONSE
            return (new OrderResource($order->load('items.product')))
                    ->additional(['message' => 'Order placed successfully and confirmation email sent!']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // 3. Get Order Details (Requirement 4.3)
    public function show($id) {
        // Ensure user can only see THEIR OWN order
        $order = auth()->user()->orders()->with('items.product')->findOrFail($id);
        return new OrderResource($order);
    }
}