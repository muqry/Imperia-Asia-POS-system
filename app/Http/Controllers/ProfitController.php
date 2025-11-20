<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProfitController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->is_admin != 1) {
            abort(403, 'Unauthorized access');
        }

        $rawProfits = DB::table('order__details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('transactions as t', 'o.id', '=', 't.order_id')
            ->join('users as u', 't.user_id', '=', 'u.id') // cashier
            ->select(
                DB::raw("DATE_FORMAT(od.created_at, '%M %Y') as month"),
                'u.id as cashier_id',
                'u.name as cashier_name',
                DB::raw("CAST(SUM(od.unitprice * od.quantity * (1 - od.discount / 100)) AS DECIMAL(10,2)) as total_profit")
            )
            ->groupBy('month', 'cashier_id', 'cashier_name')
            ->orderBy(DB::raw("MAX(od.created_at)"), 'DESC')
            ->get();

        $groupedProfits = $rawProfits->groupBy('month');

        return view('profit.index', ['profits' => $groupedProfits]);
    }
}
