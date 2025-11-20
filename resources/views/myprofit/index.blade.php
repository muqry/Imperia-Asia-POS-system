@extends('layouts.app') {{-- Or your cashier layout --}}

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-white px-3 py-2 rounded" style="background-color: #860303ff;">
            💰{{ $user->name }} Profit Overview
        </h2>
    </div>

    <div class="row mb-4">
        <!-- Cashier Info Card -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-gradient bg-black text-white">
                    <h5 class="mb-0">Cashier Info</h5>
                </div>
                <div class="card-body">
                    <h5 class="fw-semibold">{{ $user->name }}</h5>
                    <h5 class="mb-0 text-muted">{{ $user->email }}</h5>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="col-md-6">
            <a href="{{ route('staff.products') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-gradient bg-black text-white">
                        <h5 class="mb-0">Products</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <h5 class="text-primary fw-bold">View Product's Stocks</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <form method="GET" action="{{ $isAdminView ? url('/admin/cashier-profit/' . $user->id) : route('myprofit.index') }}" class="mb-4">

        <div class="row align-items-center">
            <div class="col-md-4">
                <select name="month" class="form-select" onchange="this.form.submit()">
                    @foreach($months as $key => $label)
                    <b>
                        <option value="{{ $key }}" {{ $key == $selectedMonth ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    </b>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 text-end">
                <span class="badge bg-primary px-3 py-2 fs-6">
                    {{ strtoupper($months[$selectedMonth]) }} Total:
                    <strong class="text-white">RM{{ number_format($totalProfit, 2) }}</strong>
                </span>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient bg-black text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daily Breakdown</h5>
            <span class="badge bg-light text-dark">{{ count($dailyProfits) }} Day{{ count($dailyProfits) > 1 ? 's' : '' }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase text-muted small">
                            <th scope="col">Date</th>
                            <th scope="col">Total Profit (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyProfits as $date => $profit)
                        <tr>
                            <td class="align-middle">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                            <td class="align-middle text-success fw-bold">RM{{ number_format($profit, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">
                                No profit recorded for this month.
                            </td>
                        </tr>
                        @endforelse
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
        background-image: linear-gradient(to right, #0d6efd, #6610f2);
    }

    .badge.bg-primary {
        background-image: linear-gradient(to right, #198754, #0d6efd);
        color: #fff;
    }

    .card:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }
</style>