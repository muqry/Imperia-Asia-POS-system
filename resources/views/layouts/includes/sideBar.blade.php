<nav class="active" id="sidebar">
    <ul class="list-unstyled lead">

        {{-- ADMIN ONLY --}}
        @if(auth()->check() && auth()->user()->is_admin == 1)
        <li><a href="{{ route('home') }}"><i class="fa fa-home fa-lg"></i> Home</a></li>
        <li><a href="{{ route('users.index') }}"><i class="fa fa-user fa-lg"></i> Users</a></li>
        <li><a href="{{ route('products.index') }}"><i class="fa fa-tags fa-lg"></i> Products</a></li>
        <li><a href="{{ route('reports.index') }}"><i class="fa fa-file fa-lg"></i> Reports</a></li>
        <li><a href="{{ route('profit.index') }}"><i class="fa fa-chart-line fa-lg"></i> Profit</a></li>
        <li><a href="{{ route('transactions.index') }}"><i class="fa fa-receipt fa-lg"></i> Transactions</a></li>
        @endif

        {{-- ADMIN & CASHIER --}}
        @if(auth()->check() && (auth()->user()->is_admin == 1 || auth()->user()->is_admin == 2))
        <li><a href="{{ route('orders.index') }}"><i class="fa fa-laptop fa-lg"></i> Cashier</a></li>
        <li><a href="{{ route('products.barcode') }}"><i class="fa fa-barcode fa-lg"></i> Barcode</a></li>
        @endif

        {{-- CASHIER ONLY --}}
        @if(auth()->check() && auth()->user()->is_admin == 2)
        <li><a href="{{ route('myprofit.index') }}"><i class="fa fa-chart-line fa-lg"></i> My Profit</a></li>
        @endif

    </ul>
</nav>

<style>
    #sidebar ul.lead {
        border-bottom: 1px solid #47748b;
        width: fit-content;
        padding-left: 0;
    }

    #sidebar ul li a {
        padding: 10px 15px;
        font-size: 1.1em;
        display: block;
        width: 100%;
        color: #008B8B;
        transition: background 0.2s ease-in-out, color 0.2s ease-in-out;
    }

    #sidebar ul li a:hover {
        color: #fff;
        background: #008B8B;
        text-decoration: none !important;
    }

    #sidebar ul li a i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    #sidebar ul li.active>a,
    a[aria-expanded="true"] {
        color: #fff;
        background: #008B8B;
    }
</style>