<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Report - Monthly</title>
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

    <h2>Order Report - Monthly Summary</h2>

    @foreach($monthlySummary as $month => $products)
    <h3>Date: {{ $month }}</h3>
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
                <td>RM {{ $product['unit_price'] }}</td>
                <td>RM {{ $product['amount'] }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

</body>

</html>