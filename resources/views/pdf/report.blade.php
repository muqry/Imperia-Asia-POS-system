<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .section {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

    <h2>Order Report - {{ ucfirst($filter) }}</h2>

    @if($orders->isEmpty())
    <p style="text-align:center; margin-top:20px;">No orders found for this period.</p>
    @else
    @foreach($orders as $order)
    <div class="section">
        <strong>Customer Name:</strong> {{ $order->name }}<br>
        <strong>Phone:</strong> {{ $order->address }}<br>
        <strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Amount</th>
                    <th>Discount</th>
                </tr>
            </thead>
            <!---->
            <tbody>
                @foreach($order->details as $detail)
                @php
                $discount = $detail->discount ?? 0;
                $discountedAmount = $detail->unitprice * $detail->quantity * (1 - ($discount / 100));
                @endphp
                <tr>
                    <td>{{ $detail->product->product_name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>RM {{ number_format($detail->unitprice, 2) }}</td>
                    <td>RM {{ number_format($discountedAmount, 2) }}</td>
                    <td>{{ $discount }}%</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
    @endforeach
    @endif


</body>

</html>