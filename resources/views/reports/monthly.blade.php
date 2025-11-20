@php
$monthlySummary = $orders->flatMap(fn($order) => $order->details)
    ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->created_at)->format('F'))
    ->map(fn($group) => $group->groupBy('product_id')->map(function ($items) {
        $product = $items->first()->product;
        return [
            'product_name' => $product->product_name,
            'quantity' => $items->sum('quantity'),
            'unit_price' => $product->price,
            'amount' => $items->sum('amount'),
        ];
    }));
@endphp

@foreach($monthlySummary as $month => $products)
    <h5>Date: {{ $month }}</h5>
    <table class="table table-bordered table-left">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product['product_name'] }}</td>
                    <td>{{ $product['quantity'] }}</td>
                    <td>RM {{ number_format($product['unit_price'], 2) }}</td>
                    <td>RM {{ number_format($product['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach
