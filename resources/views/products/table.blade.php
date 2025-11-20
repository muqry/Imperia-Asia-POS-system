@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert" style="font-weight: 500; font-size: 15px;">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


<table class="table table-bordered table-left">
    <thead>
        <tr>
            <th style="background-color: #8603036e; color: #fff;">#</th>
            <th style="background-color: #8603036e; color: #fff;">Product Name</th>
            <th style="background-color: #8603036e; color: #fff;">Brand</th>
            <th style="background-color: #8603036e; color: #fff;">Price</th>
            <th style="background-color: #8603036e; color: #fff;">Quantity</th>
            <th style="background-color: #8603036e; color: #fff;">Alert Stock</th>
            <th style="background-color: #8603036e; color: #fff;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $key => $product)
        <tr>
            <td>{{ $key+1 }}</td>

            <!--Product Name + product detail-->
            <td style="cursor: pointer;" data-toggle="tooltip"
                data-placement="right" title="Click To View Detail"
                wire:click="ProductDetails({{ $product->id }})">
                {{ $product->product_name }}
            </td>

            <!--brand-->
            <td>{{ $product->brand }}</td>

            <!--price-->
            <td>{{ number_format($product->price,2) }}</td>

            <!--quantity-->
            <td>{{ $product->quantity }}</td>

            <!--alert stock-->
            <td style="text-align: center;">
                @if ($product->quantity == 0)
                <span class="badge" style="background-color: black; color: white; padding: 6px 12px; min-width: 120px; display: inline-block; text-align: center;">
                    PRODUCT = 0
                </span>
                @elseif ($product->alert_stock >= $product->quantity)
                <span class="badge bg-danger" style="padding: 6px 12px; min-width: 120px; display: inline-block; text-align: center;">
                    Low Stock > {{ $product->alert_stock }}
                </span>
                @else
                <span class="badge bg-success" style="padding: 6px 12px; min-width: 120px; display: inline-block; text-align: center;">
                    {{ $product->alert_stock }}
                </span>
                @endif
            </td>


            <td>
                <div class="btn-group">
                    <!--edit-->
                    <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editproduct{{ $product->id }}"><i class="fa fa-edit"></i>Edit</a>
                    <!--Delete-->
                    <a href="#" data-toggle="modal" data-target="#deleteproduct{{ $product->id }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>Delete</a>
                    <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#addstock{{ $product->id }}">
                        <i class="fa fa-plus"></i> Add Stock
                    </a>
                </div>
            </td>
        </tr>


        <!-- EDIT -->
        {{-- Modal of EDIT product Detail --}}

        @include('products.edit')


        <!-- DELETE -->
        {{-- Modal of DELETE product --}}

        @include('products.delete')


        <!-- ADD STOCK -->
        {{-- Modal of ADD STOCK --}}
        @include('products.addstock', ['product' => $product, 'isStaff' => false])


        @endforeach

    </tbody>
</table>

