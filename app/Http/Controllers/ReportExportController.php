<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use Illuminate\Support\Facades\Response;

class ReportExportController extends Controller
{
    public function export($filter, Request $request)
    {
        $query = Order::with(['details.product']);

        if ($filter === 'daily') {
            $date = $request->input('date') ?? now()->format('Y-m-d');
            $orders = $query->whereDate('created_at', $date)->get();

            return Pdf::loadView('pdf.report', [
                'orders' => $orders,
                'filter' => $filter,
                'date' => $date
            ])->download('daily-report-' . $date . '.pdf');
        }


        // MONTHLY REPORT
        if ($filter === 'monthly') {
            $orders = $query->get();

            $monthlySummary = $orders->flatMap(fn($order) => $order->details)
                ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->created_at)->format('F'))
                ->map(fn($group) => $group->groupBy('product_id')->map(function ($items) {
                    $productModel = $items->first()->product;

                    // Fallbacks if product is deleted
                    $productName = $productModel ? $productModel->product_name : ($items->first()->product_name ?? 'Deleted Product');
                    $productPrice = $productModel ? $productModel->price : ($items->first()->unitprice ?? 0);

                    $amount = $items->sum(function ($item) {
                        return $item->unitprice * $item->quantity * (1 - ($item->discount / 100));
                    });

                    return [
                        'product_name' => $productName,
                        'quantity' => $items->sum('quantity'),
                        'unit_price' => number_format($productPrice, 2),
                        'amount' => number_format($amount, 2),
                    ];
                }));



            return Pdf::loadView('pdf.report_monthly', [
                'monthlySummary' => $monthlySummary,
                'filter' => $filter
            ])->download('report.pdf');
        }

        // ✅ YEARLY REPORT
        if ($filter === 'yearly') {
            $orders = $query->get(); // Get all orders

            $yearlySummary = $orders->flatMap(fn($order) => $order->details)
                ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->created_at)->format('Y'))
                ->map(fn($group) => $group->groupBy('product_id')->map(function ($items) {
                    $productModel = $items->first()->product;

                    // Fallbacks if product is deleted
                    $productName = $productModel ? $productModel->product_name : ($items->first()->product_name ?? 'Deleted Product');
                    $productPrice = $productModel ? $productModel->price : ($items->first()->unitprice ?? 0);

                    $amount = $items->sum(function ($item) {
                        return $item->unitprice * $item->quantity * (1 - ($item->discount / 100));
                    });

                    return [
                        'product_name' => $productName,
                        'quantity' => $items->sum('quantity'),
                        'unit_price' => number_format($productPrice, 2),
                        'amount' => number_format($amount, 2),
                    ];
                }));


            return Pdf::loadView('pdf.report_yearly', [
                'yearlySummary' => $yearlySummary,
                'filter' => $filter
            ])->download('report.pdf');
        }
    }


    public function exportCsv(Request $request, $filter)
    {
        // Dynamic filename logic
        if ($filter === 'daily') {
            $date = $request->input('date') ?? now()->format('Y-m-d');
            $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
            $filename = "report_daily_{$formattedDate}.csv";
        } else {
            $filename = "report_{$filter}_" . now()->format('Ymd_His') . ".csv";
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($filter, $request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // Excel-friendly BOM

            if ($filter === 'daily') {
                $date = $request->input('date') ?? now()->format('Y-m-d');
                $orders = Order::with('details.product')
                    ->whereDate('created_at', $date)
                    ->get();

                // Add date label
                fputcsv($handle, ["Order Date: " . \Carbon\Carbon::parse($date)->format('d M Y')]);
                fputcsv($handle, []); // Spacer row

                fputcsv($handle, ['Customer Name', 'Phone', 'Product', 'Qty', 'Unit Price', 'Amount', 'Discount']);

                foreach ($orders as $order) {
                    foreach ($order->details as $detail) {
                        $amount = $detail->unitprice * $detail->quantity * (1 - ($detail->discount / 100));
                        fputcsv($handle, [
                            $order->name,
                            $order->address,
                            $detail->product->product_name ?? 'Deleted Product',
                            $detail->quantity,
                            number_format($detail->unitprice, 2),
                            number_format($amount, 2),
                            $detail->discount . '%',
                        ]);
                    }
                    fputcsv($handle, []); // Spacer after each customer
                }
            } elseif ($filter === 'monthly') {
                $orders = Order::with('details.product')
                    ->whereYear('created_at', now()->year)
                    ->get();

                $monthlySummary = $orders->flatMap(fn($order) => $order->details)
                    ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->created_at)->format('Y-m'))
                    ->map(fn($group) => $group->groupBy('product_id')->map(function ($items) {
                        $product = $items->first()->product;
                        return [
                            'product_name' => $product->product_name ?? 'Deleted Product',
                            'quantity' => $items->sum('quantity'),
                            'unit_price' => $product->price ?? $items->first()->unitprice,
                            'amount' => $items->sum(fn($item) => $item->unitprice * $item->quantity * (1 - ($item->discount / 100))),
                        ];
                    }));

                fputcsv($handle, ['Month', 'Product', 'Quantity', 'Unit Price', 'Total Amount']);

                foreach ($monthlySummary as $month => $products) {
                    foreach ($products as $product) {
                        fputcsv($handle, [
                            $month,
                            $product['product_name'],
                            $product['quantity'],
                            number_format($product['unit_price'], 2),
                            number_format($product['amount'], 2),
                        ]);
                    }
                    // Spacer row between months
                    fputcsv($handle, []);
                }
            } elseif ($filter === 'yearly') {
                $orders = Order::with('details.product')
                    ->whereYear('created_at', now()->year)
                    ->get();

                $yearlySummary = $orders->flatMap(fn($order) => $order->details)
                    ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->created_at)->format('Y'))
                    ->map(fn($group) => $group->groupBy('product_id')->map(function ($items) {
                        $product = $items->first()->product;
                        return [
                            'product_name' => $product->product_name ?? 'Deleted Product',
                            'quantity' => $items->sum('quantity'),
                            'unit_price' => $product->price ?? $items->first()->unitprice,
                            'amount' => $items->sum(fn($item) => $item->unitprice * $item->quantity * (1 - ($item->discount / 100))),
                        ];
                    }));

                fputcsv($handle, ['Year', 'Product', 'Quantity', 'Unit Price', 'Total Amount']);

                foreach ($yearlySummary as $year => $products) {
                    foreach ($products as $product) {
                        fputcsv($handle, [
                            $year,
                            $product['product_name'],
                            $product['quantity'],
                            number_format($product['unit_price'], 2),
                            number_format($product['amount'], 2),
                        ]);
                    }
                }
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
