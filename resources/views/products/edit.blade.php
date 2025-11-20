<div class="modal right fade" id="editproduct{{ $product->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Edit product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ $product->id }}
            </div>
            <div class="modal-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="">Product Name</label>
                        <input type="text" name="product_name" id="" value="{{ $product->product_name }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Product Code</label>
                        <input type="text" name="product_code" id="" value="{{ $product->product_code }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Brand</label>
                        <input type="text" name="brand" id="" value="{{ $product->brand }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Price</label>
                        <input type="number" name="price" id="" value="{{ $product->price }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Quantity</label>
                        <input type="number" name="quantity" id="" value="{{ $product->quantity }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Alert Stock</label>
                        <input type="number" name="alert_stock" id="" value="{{ $product->alert_stock }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Description</label>
                        <textarea name="description" id="" cols="30" rows="2" class="form-control">{{ $product->description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Image</label>
                        <img width="40" src="{{ asset('product/images/' .$product->product_image ) }}" alt="">
                        <input type="file" name="product_image" id="" class="form-control">
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-warning btn-block">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>