<nav class="navbar navbar-expand-lg bg-black shadow-sm text-white">
    <div class="container-fluid">
        <div class="navbar-brand">
            <img src="{{ url('build/assets/logo1.png') }}" alt="POS Logo" class="logo-img">
        </div>
        {{--<a class="navbar-brand" href="{{ route('home') "}}>POS System</a>--}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

                <!--ADMIN JE-->
                @if(auth()->check() && auth()->user()->is_admin == 1)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}"><i class="fa fa-user"></i>Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}"><i class="fa fa-tags"></i>Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}"><i class="fa fa-file"></i>Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profit.index') }}"><i class="fa fa-chart-line"></i> Profit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('transactions.index') }}"><i class="fa fa-receipt"></i> Transaction</a>
                </li>
                @endif


                <!--ADMIN DAN CASHIER-->
                @if(auth()->check() && (auth()->user()->is_admin == 1 || auth()->user()->is_admin == 2))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}"><i class="fa fa-laptop"></i>Cashier</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.barcode') }}"><i class="fa fa-barcode"></i>Barcode</a>
                </li>
                @endif


                <!--CASHIER JE-->
                @if(auth()->check() && auth()->user()->is_admin == 2)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('myprofit.index') }}"><i class="fa fa-chart-line"></i>My Profit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('staff.products') }}"><i class="fa fa-boxes"></i>Product Stock</a>
                </li>
                @endif


            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar-nav .nav-link {
        font-weight: 500;
        color: #fff !important;
    }

    .navbar-nav .nav-link:hover {
        color: #0d6efd !important;
    }

    .navbar-nav .nav-link:hover {
        color: #007bff;
    }

    .logo-img {
        height: 50px;
        /* Controls vertical size */
        width: auto;
        /* Keeps aspect ratio */
        margin-right: 10px;
        /* Adds spacing from nav items */
        vertical-align: middle;
        object-fit: contain;
        /* Ensures clean scaling */
    }

    .navbar-brand {
        display: flex;
        align-items: center;
    }
</style>