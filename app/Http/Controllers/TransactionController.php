<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Order;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));
        $method = $request->input('method');

        // 🔄 Build base query with filters
        $query = Transaction::with(['order.details.product']) // ✅ Include order details and product for discount calculation
            ->whereMonth('transac_date', $month)
            ->whereYear('transac_date', $year)
            ->orderBy('created_at', 'desc');

        if ($method) {
            $query->where('payment_method', $method);
        }

        $transactions = $query->get();

        // ✅ Calculate true discounted amount for each transaction
        $transactions = $transactions->map(function ($trans) {
            $details = $trans->order->details;

            // ✅ Calculate discounted total from order details
            $trueAmount = $details->sum(function ($item) {
                return (float) $item->unitprice * $item->quantity * (1 - ($item->discount / 100));
            });

            // ✅ Assume paid_amount equals trueAmount if balance is 0
            $paidAmount = $trans->balance == 0 ? $trueAmount : (float) $trans->paid_amount;

            // ✅ Recalculate balance to ensure consistency
            $balance = $trueAmount - $paidAmount;

            // ✅ Attach calculated values to transaction object
            $trans->true_amount = $trueAmount;
            $trans->paid_amount = $paidAmount;
            $trans->balance = $balance;

            return $trans;
        });


        // ✅ Recalculate totals based on true discounted amounts
        $cashTotal = $transactions->where('payment_method', 'cash')->sum('true_amount');
        $bankTotal = $transactions->where('payment_method', 'bank transfer')->sum('true_amount');
        $cardTotal = $transactions->where('payment_method', 'credit card')->sum('true_amount');

        // ✅ Pass everything to the view
        return view('transactions.index', compact(
            'transactions',
            'month',
            'year',
            'cashTotal',
            'bankTotal',
            'cardTotal',
            'method'
        ));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(transaction $transaction)
    {
        //
    }
}
