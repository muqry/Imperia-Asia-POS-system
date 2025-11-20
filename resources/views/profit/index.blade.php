@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-white px-3 py-2 rounded" style="background-color: #860303ff;">
            📊 Monthly Profit by Cashier
        </h2>
    </div>


    @foreach($profits as $month => $entries)
    <div class="card mb-5 shadow-sm border-0">
        <div class="card-header bg-gradient bg-black text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ strtoupper($month) }}</h5>
            <span class="badge bg-light text-dark">{{ $entries->count() }} Cashier{{ $entries->count() > 1 ? 's' : '' }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase text-muted small">
                            <th scope="col">#</th>
                            <th scope="col">Cashier Name</th>
                            <th scope="col">Total Profit (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $index => $entry)
                        <tr onclick="window.location='{{ url('/admin/cashier-profit/' . $entry->cashier_id . '?month=' . urlencode(\Carbon\Carbon::parse($month)->format('Y-m')) ) }}'" style="cursor:pointer;">
                            <td class="align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle fw-semibold">{{ $entry->cashier_name }}</td>
                            <td class="align-middle text-success fw-bold">RM{{ number_format($entry->total_profit, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
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
</style>