<div>
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('products.index') }}" class="text-decoration-none text-white">
                                    <h4 class="mb-0" style="cursor: pointer;">Products List</h4>
                                </a>

                                {{-- Pagination controls 
                                <div>
                                    <div class="mt-3">
                                        {{ $products->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>--}}
                            </div>
                            <a href="" class="btn btn-dark" data-toggle="modal" data-target="#addproduct">
                                <i class="fa fa-plus"></i> Add New Product
                            </a>
                        </div>

                        <div class="card-body">
                            @include('products.table')
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