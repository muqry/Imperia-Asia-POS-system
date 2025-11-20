<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Report - Yearly</title>
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
            margin-bottom: 30px;
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
    </style>
</head>

<body>

    <h2>Order Report - Yearly Summary</h2>

    @if($yearlySummary->isEmpty())
        <p>No orders found for this year.</p>
    @else
    @foreach($yearlySummary as $year => $products)
    <h3>Year: {{ $year }}</h3>
    <table>
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
                <td>RM {{ is_numeric($product['amount']) ? number_format((float) $product['amount'], 2) : $product['amount'] }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
    @endif


</body>

</html>