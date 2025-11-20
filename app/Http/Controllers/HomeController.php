<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Order_Detail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // dd(Auth::user());

        $inStockCount = Product::whereColumn('quantity', '>', 'alert_stock')->count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'alert_stock')->count();

        $mostSoldProduct = DB::table('order__details')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->first();

        $mostSoldName = $mostSoldProduct
            ? Product::find($mostSoldProduct->product_id)?->product_name ?? 'Unknown'
            : 'No Sales Yet';

        // bar cart
        $year = $request->input('year', date('Y'));

        //$transactions = transaction::select(
        //DB::raw('MONTH(transac_date) as month'),
        //DB::raw('SUM(transac_amount) as total')
        //)
        //->whereYear('transac_date', $year)
        //->groupBy('month')
        //->orderBy('month')
        //->get();

        $orderDetails = Order_Detail::whereYear('created_at', $year)->get();

        $monthlyTotals = collect($orderDetails)
            ->groupBy(fn($detail) => Carbon::parse($detail->created_at)->month)
            ->map(fn($group) => $group->sum(function ($item) {
                return $item->unitprice * $item->quantity * (1 - ($item->discount / 100));
            }));

        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        //$labels = [];
        //$data = [];

        //for ($i = 1; $i <= 12; $i++) {
        //$labels[] = $months[$i];
        //$monthData = $transactions->firstWhere('month', $i);
        //$data[] = $monthData ? $monthData->total : 0;
        // Find best month
        //$bestMonthData = collect($transactions)->sortByDesc('total')->first();
        //$bestMonthName = $bestMonthData ? $months[$bestMonthData->month] : 'No Data';
        //$bestMonthAmount = $bestMonthData ? $bestMonthData->total : 0;
        //}

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $months[$i];
            $data[] = round($monthlyTotals[$i] ?? 0, 2);
        }

        $bestMonthAmount = collect($data)->max();
        $bestMonthIndex = array_search($bestMonthAmount, $data);
        $bestMonthName = $months[$bestMonthIndex + 1]; // +1 because months start at 1
        $bestMonthAmount = $data[$bestMonthIndex];

        $lowStockProducts = Product::whereColumn('quantity', '<=', 'alert_stock')->get();



        // Calculate today's total sales
        $today = Carbon::today();
        $todayTotalSales = Order_Detail::whereDate('created_at', $today)
            ->get()
            ->sum(function ($detail) {
                return $detail->unitprice * $detail->quantity * (1 - ($detail->discount / 100));
            });

        
        // calculate year total 
        $yearlyTotalSales = collect($data)->sum();

        //total product
        $totalProductCount = Product::count();


        

        //session()->flash('status', 'Welcome back! Dashboard is ready.');
        return view('home', compact(
            'inStockCount',
            'lowStockCount',
            'lowStockProducts', // cursor bila hover dkt low stock cards 
            'mostSoldName', // best seller product
            'labels',
            'data',
            'year',
            'todayTotalSales', // today total sales cards
            'bestMonthName', // best month 
            'bestMonthAmount', // best month total amount
            'yearlyTotalSales', //yearly sales total
            'totalProductCount' //total product
        ));
    }
}
