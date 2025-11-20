@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <h4 class="card-header" style="background: #860303ff; color: #fff">
                    <marquee behavior="" direction="">Welcome To Imperia Asia Point Of Sales Management System</marquee>
                </h4>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                </div>
            </div>

            <br>

            {{-- Unified Sales Section --}}
            <div class="card shadow-lg mb-4 mx-auto" style="background-color: #f8f9fa; max-width: 95%;">
                <div class="card-body text-center">

                    {{-- Total Sales Cards Row --}}
                    <div class="row justify-content-center mb-4">
                        {{-- Today Sales --}}
                        <div class="col-md-4">
                            <div class="card shadow-sm" style="border-left: 5px solid #4b2a91; background-color: #6f42c1; color: #ffffff;">
                                <div class="card-body text-center py-3">
                                    <div class="fs-5 fw-bold">RM {{ number_format($todayTotalSales, 2) }}</div>
                                    <div class="text-uppercase small">Today Sales</div>
                                </div>
                            </div>
                        </div>

                        {{-- Best Month --}}
                        <div class="col-md-4">
                            <div class="card shadow-sm" style="border-left: 5px solid #ffc107; background-color: #ffeb99; color: #212529;">
                                <div class="card-body text-center py-3">
                                    <div class="fs-5 fw-bold">{{ $bestMonthName }} = RM {{ number_format($bestMonthAmount, 2) }}</div>
                                    <div class="text-uppercase small">Best Month</div>
                                </div>
                            </div>
                        </div>

                        {{-- Yearly Total --}}
                        <div class="col-md-4">
                            <div class="card shadow-sm" style="border-left: 5px solid #17a2b8; background-color: #c6f0f8; color: #0c5460;">
                                <div class="card-body text-center py-3">
                                    <div class="fs-5 fw-bold">RM {{ number_format($yearlyTotalSales, 2) }}</div>
                                    <div class="text-uppercase small" style="letter-spacing: 1px;">Yearly Total</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sales Report Chart --}}
                    <div class="card shadow-sm mx-auto" style="max-width: 95%;">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <strong>Report Sales</strong>
                            <form method="GET" action="{{ route('home') }}" class="mb-0">
                                <select name="year" onchange="this.form.submit()" class="form-select form-select-sm">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" @if($year==$y) selected @endif>{{ $y }}</option>
                                    @endfor
                                </select>
                            </form>
                        </div>
                        <div class="card-body">
                            <canvas id="transactionChart" style="height: 400px;"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!---------------LEFT SIDE-------------->

        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-header" style="background-color: #000000ff; color: #fff;">
                    <strong>Dashboard</strong>
                </div>
                <div class="card-body p-2">

                    <br>
                    {{-- In Stock Card (Styled Like Others) --}}
                    <div class="card mb-3 shadow-sm" style="border-left: 5px solid #28a745; background-color: #d4edda; color: #155724;">
                        <div class="card-body text-center py-3">
                            <div class="fs-5 fw-bold">{{ $inStockCount }}</div>
                            <div class="text-uppercase small" style="letter-spacing: 1px;">In Stock</div>
                        </div>
                    </div>

                    {{-- Neon Wrapper for Low Stock --}}
                    <div class="neon-wrapper mb-3 low-stock-tooltip">
                        <div class="card shadow-sm" style="background-color: #f8d7da; color: #721c24; border-radius: 8px;">
                            <div class="card-body text-center py-3">
                                <div class="fs-5 fw-bold">{{ $lowStockCount }}</div>
                                <div class="text-uppercase small" style="letter-spacing: 1px;">Low Stock</div>
                            </div>

                            {{-- Tooltip Content --}}
                            <div class="tooltip-content">
                                @forelse ($lowStockProducts as $product)
                                <div>{{ $product->product_name }} ({{ $product->quantity }})</div>
                                @empty
                                <div>No low stock items</div>
                                @endforelse
                            </div>
                        </div>
                    </div>


                    {{-- Most Seller Card --}}
                    <div class="card mb-3 shadow-sm" style="border-left: 5px solid #0056b3; background-color: #d6e9ff; color: #003366;">
                        <div class="card-body text-center py-3">
                            <div class="fs-5 fw-bold">{{ $mostSoldName }}</div>
                            <div class="text-uppercase small" style="letter-spacing: 1px;">Most Seller</div>
                        </div>
                    </div>


                    {{-- Total Products Card --}}
                    <div class="card mb-3 shadow-sm" style="border-left: 5px solid #20c997; background-color: #e0f7f4; color: #0f4c4c;">
                        <div class="card-body text-center py-3">
                            <div class="fs-5 fw-bold">{{ $totalProductCount }}</div>
                            <div class="text-uppercase small" style="letter-spacing: 1px;">Total Products</div>
                        </div>
                    </div>


                </div> {{-- "card-body p-2" --}}


                {{--<div class="card-header" style="background-color: #000000ff; color: #fff;">
                    <strong>donno yet what to put here</strong>
                </div>
                <div class="card-body p-2">

                    

                </div>{{-- "card-body p-2" --}}
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = @json($labels);
    const data = {
        labels: labels,
        datasets: [{
            label: 'Transaction Amount',
            data: @json($data),
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Transactions'
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const transactionChart = new Chart(
        document.getElementById('transactionChart'),
        config
    );
</script>



<style>
    .neon-wrapper {
        padding: 2px;
        border-radius: 10px;
        animation: pulseNeon 1.5s infinite alternate;
        box-shadow:
            0 0 5px #e33338,
            0 0 15px #e33338,
            0 0 30px #e33338,
            0 0 50px #e33338,
            0 0 70px #e33338;
    }

    @keyframes pulseNeon {
        0% {
            box-shadow:
                0 0 5px #e33338,
                0 0 15px #e33338,
                0 0 30px #e33338,
                0 0 50px #e33338,
                0 0 70px #e33338;
        }

        100% {
            box-shadow:
                0 0 10px #ff4d4d,
                0 0 30px #ff4d4d,
                0 0 60px #ff4d4d,
                0 0 90px #ff4d4d,
                0 0 120px #ff4d4d;
        }
    }



    /** ni untuk bila cursor hover low stock cards */
    .low-stock-tooltip .tooltip-content {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: #1c1c1c;
        color: #fff;
        padding: 10px;
        font-size: 0.9rem;
        border-radius: 5px;
        box-shadow: 0 0 10px #ff4d4d;
        z-index: 10;
        animation: fadeIn 0.3s ease-in-out;
    }

    .low-stock-tooltip:hover .tooltip-content {
        display: block;
    }

    .low-stock-tooltip {
        position: relative;
        z-index: 10;
        overflow: visible;
    }

    .tooltip-content {
        z-index: 999;
    }


    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /**00 */
    .card-body {
        overflow: visible !important;
    }
</style>


@endsection