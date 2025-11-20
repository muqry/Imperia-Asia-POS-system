@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Title and Month Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2" style="background-color: #860303;">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('transactions.index', ['month' => $month, 'year' => $year]) }}" class="text-decoration-none">
                <h2 class="fw-bold text-white px-4 py-2 rounded shadow-sm mb-0">
                    TRANSACTIONS
                </h2>
            </a>

            <!-- Summary Cards -->
            <a href="{{ route('transactions.index', ['month' => $month, 'year' => $year, 'method' => 'cash']) }}" class="text-decoration-none">
                <div class="px-3 py-2 rounded text-white fw-semibold" style="background-color: #198754;">
                    Cash = RM{{ number_format((float) $cashTotal, 2) }}
                </div>
            </a>
            <a href="{{ route('transactions.index', ['month' => $month, 'year' => $year, 'method' => 'bank transfer']) }}" class="text-decoration-none">
                <div class="px-3 py-2 rounded text-white fw-semibold" style="background-color: #0d6efd;">
                    Bank Transfer = RM{{ number_format((float) $bankTotal, 2) }}
                </div>
            </a>
            <a href="{{ route('transactions.index', ['month' => $month, 'year' => $year, 'method' => 'credit card']) }}" class="text-decoration-none">
                <div class="px-3 py-2 rounded text-dark fw-semibold" style="background-color: #ffc107;">
                    Card Credit = RM{{ number_format((float) $cardTotal, 2) }}
                </div>
            </a>
        </div>

        <!-- Month & Year Filter -->
        <form method="GET" action="{{ route('transactions.index') }}" class="d-flex align-items-center gap-2">
            <label class="fw-semibold me-2" style="color: white;">Month</label>
            <select name="month" onchange="this.form.submit()" class="form-select w-auto">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                    {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                    @endfor
            </select>

            <label class="fw-semibold ms-3 me-2" style="color: white;">Year</label>
            <select name="year" onchange="this.form.submit()" class="form-select w-auto">
                @for ($y = now()->year; $y >= 2020; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>



    <!-- Transaction Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient bg-black text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ \Carbon\Carbon::create()->month($month)->year($year)->format('F Y') }}</h5>
            <span class="badge bg-light text-dark">{{ $transactions->count() }} Transaction{{ $transactions->count() > 1 ? 's' : '' }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase text-muted small">
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Amount (RM)</th>
                            <th>Paid Amount (RM)</th>
                            <th>Balance (RM)</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $index => $trans)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $trans->order->name }}</td>
                            <td>{{ $trans->order->address }}</td>
                            <td class="text-primary">RM{{ number_format((float)$trans->true_amount, 2) }}</td>
                            <td class="text-success">RM{{ number_format((float)$trans->paid_amount, 2) }}</td>
                            <td class="text-danger">RM{{ number_format(abs((float)$trans->balance), 2) }}</td>
                            <td>
                                @php
                                $methodColor = match(strtolower($trans->payment_method)) {
                                'cash' => 'bg-success',
                                'bank transfer' => 'bg-primary',
                                'credit card' => 'bg-warning text-dark', // yellow needs dark text for contrast
                                default => 'bg-secondary',
                                };
                                @endphp
                                <span class="payment-badge {{ $methodColor }}">
                                    {{ ucfirst($trans->payment_method) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($trans->transac_date)->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

<style>
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
        transition: background-color 0.2s ease-in-out;
    }

    .card-header {
        background-image: linear-gradient(to right, #6610f2, #0d6efd);
    }

    .payment-badge {
        display: inline-block;
        min-width: 120px;
        /* consistent width */
        text-align: center;
        padding: 6px 12px;
        font-weight: 600;
        border-radius: 6px;
        color: white;
        font-size: 0.85rem;
    }

    .bg-warning.text-dark {
        color: #212529 !important;
        /* override white for yellow badge */
    }
</style>