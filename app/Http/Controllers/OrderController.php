<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_Detail;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $orders = Order::all();
        //last order details
        $lastID = Order_Detail::max('order_id');
        $order_receipt = Order_Detail::where('order_id', $lastID)->get();
        
         // ✅ Add this line to fetch balance and payment info
    $transaction = Transaction::where('order_id', $lastID)->first();

    return view('orders.index', [
        'products' => $products,
        'orders' => $orders,
        'order_receipt' => $order_receipt,
        'transaction' => $transaction
    ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //dd($request->all());
        try {
            $order_id = null;

            DB::transaction(function () use ($request) {

                // Order Modal
                $orders = new Order;
                $orders->name = $request->customer_name;
                $orders->address  = $request->customer_phone; //storing phone in address
                $orders->save();
                $order_id = $orders->id;


                //Order Details Modal

                for ($product_id = 0; $product_id < count($request->product_id); $product_id++) {

                    $order_details = new Order_Detail;
                    $order_details->order_id = $order_id;
                    $order_details->product_id = $request->product_id[$product_id];
                    $order_details->unitprice = $request->price[$product_id];
                    $order_details->quantity = $request->quantity[$product_id];
                    $order_details->discount = $request->discount[$product_id];
                    $order_details->amount = $request->total_amount[$product_id];
                    $order_details->save();

                    // 🔁 Update product stock (INSIDE the loop)
                    $product = Product::find($request->product_id[$product_id]);

                    if ($product) {
                        $newQty = $product->quantity - $request->quantity[$product_id];
                        $product->quantity = $newQty;

                        // 🚨 Trigger alert if stock is low
                        if ($newQty <= $product->alert) {
                        }

                        $product->save();
                    }
                }


                //Transactio Modal
                $transaction = new Transaction();
                $transaction->order_id = $order_id;
                $transaction->user_id = auth()->user()->id;
                $transaction->balance = $request->balance;
                $transaction->paid_amount = $request->paid_amount;
                $transaction->payment_method = $request->payment_method;
                $transaction->transac_amount = array_sum($request->total_amount);
                $transaction->transac_date = date('Y-m-d');
                $transaction->save();

                
                //clear cart
                Cart::where('user_id', auth()->user()->id)->delete();
            });


            // ✅ After transaction completes
            foreach ($request->product_id as $index => $id) {
                $product = Product::find($id);
                if ($product && $product->quantity <= $product->alert) {
                    return redirect()->route('orders.index')
                        ->with('warning', 'Stock for ' . $product->product_name . ' is low!');
                }
            }

            



            //Last Order History
            $products = Product::all();
            $order_details = Order_Detail::where('order_id', $order_id)->get();
            $orderedBy = Order::where('id', $order_id)->get();

            return redirect()->route('orders.index')->with('successful', 'Orders have been successfully made');


        } catch (\Exception $e) {
            // Only hit if something fails
            return back()->with("Product Orders Fails To Inserted! Check Your Inputs");
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
