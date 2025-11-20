<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyProfitController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get all months where the user has made profit
        $allMonthsRaw = DB::table('order__details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('transactions as t', 'o.id', '=', 't.order_id')
            ->where('t.user_id', $user->id)
            ->select(DB::raw("DATE_FORMAT(od.created_at, '%Y-%m') as month"))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->pluck('month');

        $months = $allMonthsRaw->mapWithKeys(function ($month) {
            return [$month => Carbon::parse($month . '-01')->format('F Y')];
        });

        // Fallback to latest available month if none selected or invalid
        $selectedMonth = $request->get('month');
        if (!$selectedMonth || !$months->has($selectedMonth)) {
            $selectedMonth = $months->keys()->first();
        }

        $start = Carbon::parse($selectedMonth)->startOfMonth();
        $end = Carbon::parse($selectedMonth)->endOfMonth();

        $rawProfits = DB::table('order__details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('transactions as t', 'o.id', '=', 't.order_id')
            ->where('t.user_id', $user->id)
            ->whereBetween('od.created_at', [$start, $end])
            ->select(
                DB::raw("DATE(od.created_at) as date"),
                DB::raw("CAST(SUM(od.unitprice * od.quantity * (1 - od.discount / 100)) AS DECIMAL(10,2)) as daily_profit")
            )
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $dailyProfits = $rawProfits->pluck('daily_profit', 'date');
        $totalProfit = $dailyProfits->sum();

        return view('myprofit.index', [
            'user' => $user,
            'dailyProfits' => $dailyProfits,
            'totalProfit' => $totalProfit,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
            'isAdminView' => false
        ]);
    }

    public function adminView(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->is_admin != 1) {
            abort(403, 'Unauthorized access');
        }

        $cashier = DB::table('users')->where('id', $id)->first();
        if (!$cashier) {
            abort(404, 'Cashier not found');
        }

        // Get all months where the cashier has made profit
        $allMonthsRaw = DB::table('order__details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('transactions as t', 'o.id', '=', 't.order_id')
            ->where('t.user_id', $id)
            ->select(DB::raw("DATE_FORMAT(od.created_at, '%Y-%m') as month"))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->pluck('month');

        $months = $allMonthsRaw->mapWithKeys(function ($month) {
            return [$month => Carbon::parse($month . '-01')->format('F Y')];
        });

        // Fallback to latest available month if none selected or invalid
        $selectedMonth = $request->get('month');
        if (!$selectedMonth || !$months->has($selectedMonth)) {
            $selectedMonth = $months->keys()->first();
        }

        $start = Carbon::parse($selectedMonth)->startOfMonth();
        $end = Carbon::parse($selectedMonth)->endOfMonth();

        $rawProfits = DB::table('order__details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('transactions as t', 'o.id', '=', 't.order_id')
            ->where('t.user_id', $id)
            ->whereBetween('od.created_at', [$start, $end])
            ->select(
                DB::raw("DATE(od.created_at) as date"),
                DB::raw("CAST(SUM(od.unitprice * od.quantity * (1 - od.discount / 100)) AS DECIMAL(10,2)) as daily_profit")
            )
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $dailyProfits = $rawProfits->pluck('daily_profit', 'date');
        $totalProfit = $dailyProfits->sum();

        return view('myprofit.index', [
            'user' => $cashier,
            'dailyProfits' => $dailyProfits,
            'totalProfit' => $totalProfit,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
            'isAdminView' => true
        ]);
    }
}
