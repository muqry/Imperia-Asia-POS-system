<!-- ADD STOCK MODAL -->
<div class="modal fade" id="addstock{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="addStockLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('products.addstock', $product->id) }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Stock for {{ $product->product_name }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p><strong>Current Quantity:</strong> {{ $product->quantity }}</p>
          
          
          @if (isset($isStaff) && $isStaff)
    <p class="text-muted">Note: You can only add stock. Editing and deleting are restricted to admin.</p>
  @endif

  
          <div class="form-group">
            <label for="add_quantity">Quantity to Add</label>
            <input type="number" name="add_quantity" class="form-control" min="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Stock</button>
        </div>
      </div>
    </form>
  </div>
</div>
