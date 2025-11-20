

@section('content')
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('staff.products') }}" class="text-decoration-none text-white">
    <h4 class="mb-0" style="cursor: pointer;">Products List</h4>
</a>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('products.stafftable')
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Product Details</h4>
                    </div>
                    <div class="card-body">
                        @include('Products.product_detail')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
