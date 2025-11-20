<div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="mb-0">REPORT</h4>

                <div class="d-flex flex-wrap gap-2">
                    <!-- Filter Dropdown -->
                    <select wire:model="filter" wire:change="$refresh" class="form-control w-auto">
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>

                    <!-- Date Dropdown (only for daily) -->
                    @if ($filter === 'daily')
                    <select wire:model="selectedDate" wire:change="$refresh" class="form-control w-auto">
                        @foreach($availableDates as $date)
                        <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                @if ($filter === 'daily')
                <a href="{{ route('report.export', ['filter' => 'daily', 'date' => $selectedDate]) }}" target="_blank" class="btn btn-sm btn-success">Export Daily PDF</a>
                <a href="{{ route('report.export.csv', ['filter' => 'daily', 'date' => $selectedDate]) }}" target="_blank" class="btn btn-sm btn-outline-success">Export Daily CSV</a>
                @elseif ($filter === 'monthly')
                <a href="{{ route('report.export', ['filter' => 'monthly']) }}" target="_blank" class="btn btn-sm btn-primary">Export Monthly PDF</a>
                <a href="{{ route('report.export.csv', ['filter' => 'monthly']) }}" target="_blank" class="btn btn-sm btn-outline-primary">Export Monthly CSV</a>
                @elseif ($filter === 'yearly')
                <a href="{{ route('report.export', ['filter' => 'yearly']) }}" target="_blank" class="btn btn-sm btn-warning">Export Yearly PDF</a>
                <a href="{{ route('report.export.csv', ['filter' => 'yearly']) }}" target="_blank" class="btn btn-sm btn-outline-warning">Export Yearly CSV</a>
                @endif
            </div>
        </div>
    </div>
    <br>


    @if ($filter === 'daily')
    {{-- DAILY REPORT TABLE --}}
    @php
    $filteredOrders = $orders->filter(function($order) use ($selectedDate) {
    return \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') === \Carbon\Carbon::parse($selectedDate)->format('Y-m-d');
    });
    @endphp

    @if ($filteredOrders->isEmpty())
    <div class="alert alert-warning">No orders found for {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}.</div>
    @else
    <div class="report-label daily">
        <strong>Order Date:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
    </div>

    @foreach($filteredOrders as $order)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #8603036e; color: #fff;">
            <div>
                <strong>Customer Name:</strong> {{ $order->name }} <br>
                <strong>Phone:</strong> {{ $order->address }}
            </div>
            <div>
                <strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="fw-bold">Product</th>
                        <th class="fw-bold">Quantity</th>
                        <th class="fw-bold">Unit Price</th>
                        <th class="fw-bold">Amount</th>
                        <th class="fw-bold">Discount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $detail)
                    @php
                    $discountedAmount = $detail->unitprice * $detail->quantity * (1 - ($detail->discount / 100));
                    @endphp
                    <tr>
                        <td class="fw-bold">
                            {{ $detail->product->product_name ?? 'Deleted Product' }}
                        </td>
                        <td class="fw-bold">{{ $detail->quantity }}</td>
                        <td class="fw-bold">RM {{ number_format($detail->unitprice, 2) }}</td>
                        <td class="text-success fw-bold">RM {{ number_format($discountedAmount, 2) }}</td>
                        <td class="text-primary fw-bold">{{ $detail->discount }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
    @endif

    @elseif ($filter === 'monthly')
    {{-- MONTHLY REPORT TABLE --}}
    @php
    $monthlySummary = $orders->flatMap(fn($order) => $order->details)
    ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->created_at)->format('Y-m'))
    ->sortKeysDesc()
    ->map(fn($group) => $group->groupBy('product_id')->map(function ($items) {
    $product = $items->first()->product;
    $productName = $product ? $product->product_name : ($items->first()->product_name ?? 'Deleted Product');
    $productPrice = $product ? $product->price : ($items->first()->unitprice ?? 0);

    return [
    'product_name' => $productName,
    'quantity' => $items->sum('quantity'),
    'unit_price' => $productPrice,
    'amount' => $items->sum(function ($item) {
    return $item->unitprice * $item->quantity * (1 - ($item->discount / 100));
    }),
    ];

    }));
    @endphp

    @foreach($monthlySummary as $monthKey => $products)
    <div class="report-label monthly">
        <!--<strong>Month:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('F Y') }}
        <div>Debug Month Key: {{ $monthKey }}</div>-->
        <strong>Month:</strong> {{ \Carbon\Carbon::parse($monthKey . '-01')->format('F Y') }}
    </div>

    <div class="card mb-3">
        <div class="card-header-month">
            <strong>Monthly Summary</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td class="fw-bold">{{ $product['product_name'] }}</td>
                        <td class="fw-bold">{{ $product['quantity'] }}</td>
                        <td class="fw-bold">RM {{ number_format($product['unit_price'], 2) }}</td>
                        <td class="text-success fw-bold">RM {{ number_format($product['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach


    @elseif ($filter === 'yearly')
    {{-- YEARLY REPORT TABLE --}}
    @php
    $yearlySummary = $orders->flatMap(fn($order) => $order->details)
    ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->created_at)->format('Y'))
    ->map(fn($group) => $group->groupBy('product_id')->map(function ($items) {
    $product = $items->first()->product;
    $productName = $product ? $product->product_name : ($items->first()->product_name ?? 'Deleted Product');
    $productPrice = $product ? $product->price : ($items->first()->unitprice ?? 0);

    return [
    'product_name' => $productName,
    'quantity' => $items->sum('quantity'),
    'unit_price' => $productPrice,
    'amount' => $items->sum(function ($item) {
    return $item->unitprice * $item->quantity * (1 - ($item->discount / 100));
    }),
    ];
    }));

    @endphp

    @foreach($yearlySummary as $year => $products)
    <div class="report-label yearly">
        <strong>Year:</strong> {{ $year }}
    </div>

    <div class="card mb-3">
        <div class="card-header-year">
            <strong>Yearly Summary</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td class="fw-bold">{{ $product['product_name'] }}</td>
                        <td class="fw-bold">{{ $product['quantity'] }}</td>
                        <td class="fw-bold">RM {{ number_format($product['unit_price'], 2) }}</td>
                        <td class="text-success fw-bold">RM {{ number_format($product['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
    @endif


    <style>
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
            transition: background-color 0.2s ease-in-out;
        }

        .card-header {
            background-color: #8603036e !important;
            color: #fff;
            font-weight: bold;
        }

        .report-label {
            border-radius: 6px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .report-label.daily {
            background-color: #000000ff;
            color: #fff;
        }

        .report-label.monthly {
            background-color: #003366;
            color: #fff;
        }

        .report-label.yearly {
            background-color: #4B0082;
            color: #fff;
        }

        .card-header-year {
            background-color: #6A0DAD;
            color: #fff;
            border-radius: 6px;
            padding: 0.5rem 1rem;
        }

        .card-header-month {
            background-color: #006699;
            color: #fff;
            border-radius: 6px;
            padding: 0.5rem 1rem;
        }
    </style>
</div>