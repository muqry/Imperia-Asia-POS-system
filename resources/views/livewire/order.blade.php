<div class="col-lg-12">
    <div class="row">
        <!-- LEFT SIDE: PRODUCT LIST -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 style="float: left">Order Products</h4>
                    <a href="#" style="float: right" data-toggle="modal" data-target="#addproduct">
                        <button class="btn btn-warning">
                            <div class="form-group">
                                <!--<label>Apply Discount to All Products</label>-->
                                <select class="form-control" wire:change="applyDefaultDiscount($event.target.value)">
                                    <option value="">-- Select Discount --</option>
                                    @foreach ([5,10,15,20,25,30,35,40,45,50] as $percent)
                                    <option value="{{ $percent }}">{{ $percent }}% Discount</option>
                                    @endforeach
                                </select>
                            </div>
                        </button>
                    </a>
                </div>

                <div class="card-body">
                    <!-- product input -->
                    <div class="my-2">
                        <form wire:submit.prevent="InsertoCart">
                            <select wire:model.live="product_code" class="form-control" required>
                                <option value="">-- Select Product --</option>
                                @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ $product->quantity == 0 ? 'disabled' : '' }}>
                                    {{ $product->product_name }} (Qty Left: {{ $product->quantity }})
                                </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
                        </form>
                    </div>

                    <!-- alerts -->
                    @if (session()->has('successful'))
                    <div class="alert alert-success">{{ session('successful') }}</div>
                    @elseif (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @elseif(session()->has('info'))
                    <div class="alert alert-danger">{{ session('info') }}</div>
                    @elseif(session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- product table -->
                    <table class="table table-bordered table-left">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Discount (%)</th>
                                <th colspan="6">Total</th>
                            </tr>
                        </thead>
                        <tbody class="addMoreProduct">
                            @foreach ($productIncart as $key => $cart)
                            <tr wire:key="cart-{{ $cart->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td width="30%">
                                    <input type="text" class="form-control" value="{{ $cart->product->product_name }}" readonly>
                                </td>
                                <td width="15%">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button wire:click.prevent="IncrementQty({{ $cart->id }})" class="btn btn-sm btn-success"> + </button>
                                        </div>
                                        <div class="col-md-1">
                                            <label>{{ $cart->product_qty }}</label>
                                        </div>
                                        <div class="col-md-2">
                                            <button wire:click.prevent="DecrementQty({{ $cart->id }})" class="btn btn-sm btn-danger"> - </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" value="{{ $cart->product->price }}" class="form-control" readonly>
                                </td>
                                <!-- -new codes- -->
                                <!-- Discount input bound to Livewire -->
                                <td>
                                    <input type="number" class="form-control"
                                        wire:model="discounts.{{ $cart->id }}"
                                        wire:change="saveDiscount({{ $cart->id }})"
                                        min="0" max="100">
                                </td>

                                <!-- Live total display -->
                                <td>
                                    @php
                                    // Force everything to numbers
                                    $disc = isset($discounts[$cart->id]) ? floatval($discounts[$cart->id]) : 0;
                                    $unit = floatval($cart->product->price);
                                    $qty = intval($cart->product_qty);

                                    $finalUnit = $unit - ($unit * $disc / 100);
                                    $lineTotal = $finalUnit * $qty;
                                    @endphp
                                    <input type="text" class="form-control" value="{{ number_format($lineTotal, 2, '.', '') }}" readonly>
                                </td>

                                <td>
                                    <a href="#" class="btn btn-sm btn-danger rounded-circle" wire:click="removeProduct({{ $cart->id }})">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col-md-8 -->


        <!-- RIGHT SIDE: PAYMENT SUMMARY -->
        <div class="col-md-4">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                <!------------------------->
                @foreach ($productIncart as $key => $cart)
                <input type="hidden" name="product_id[]" value="{{ $cart->product->id }}" readonly>
                <input type="hidden" name="quantity[]" value="{{ $cart->product_qty }}">
                <input type="hidden" name="price[]" value="{{ $cart->product->price }}" class="form-control price" readonly>

                <!-- send the discount value that the cashier typed (defaults to 0) -->
                <input type="hidden" name="discount[]" value="{{ $discounts[$cart->id] ?? 0 }}">

                <!-- (optional) send computed total_amount if you still rely on it on server side -->
                @php
                // Force everything to numbers
                $disc = isset($discounts[$cart->id]) ? floatval($discounts[$cart->id]) : 0;
                $unit = floatval($cart->product->price);
                $qty = intval($cart->product_qty);

                $finalUnit = $unit - ($unit * $disc / 100);
                $lineTotal = $finalUnit * $qty;
                @endphp
                <input type="hidden" name="total_amount[]" value="{{ number_format($lineTotal, 2, '.', '') }}" class="form-control total_amount" readonly>
                @endforeach

                <!------------------------->

                <div class="card">
                    <!---@php
                    $grandTotal = 0;

                    foreach ($productIncart as $cart) {
                    $disc = isset($discounts[$cart->id]) ? floatval($discounts[$cart->id]) : 0;
                    $unit = floatval($cart->product->price);
                    $qty = intval($cart->product_qty);

                    $finalUnit = $unit - ($unit * $disc / 100);
                    $grandTotal += $finalUnit * $qty;
                    }
                    @endphp---->

                    <div class="card-header">
                        <h4>Total <b class="total">{{ number_format($grandTotal, 2, '.', '') }}</b></h4>
                    </div>
                    <div class="card-body">
                        <!-- buttons -->
                        <div class="btn-group">
                            <button type="button" onclick="PrintReceiptContent('receiptContent')" class="btn btn-dark">
                                <i class="fa fa-print"></i> Print
                            </button>
                            <!--<button type="button" class="btn btn-primary">
                                <i class="fa fa-history"></i> History
                            </button>-->
                            <a href="{{ route('myprofit.index') }}" class="btn btn-warning">
                                <i class="fa fa-file"></i> My Profit
                            </a>
                        </div>

                        <!-- customer info -->
                        <div class="form-group mt-3">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Customer Phone</label>
                            <input type="number" name="customer_phone" class="form-control" required>
                        </div>

                        <!-- payment method -->
                        <div class="form-group">
                            <label>Payment Method</label><br>
                            <label>
                                <input type="radio" wire:click="refreshTotals" wire:model="payment_method" value="cash" checked>
                                <i class="fa fa-money-bill text-success"></i> Cash
                            </label>
                            <label>
                                <input type="radio" wire:click="refreshTotals" wire:model="payment_method" value="bank transfer">
                                <i class="fa fa-university text-danger"></i> QR Transfer
                            </label>
                            <label>
                                <input type="radio" wire:click="refreshTotals" wire:model="payment_method" value="credit card">
                                <i class="fa fa-credit-card text-info"></i> Credit Card
                            </label>
                        </div>

                        @if ($payment_method === 'bank transfer')
                        <div x-show="$wire.payment_method === 'bank transfer'" x-transition>
                            {{--<label>Scan QR Bank</label>--}}
                            <img src="{{ asset('build/assets/qr.jpg') }}" alt="Bank QR Code" class="img-fluid" style="max-width: 200px;">
                        </div>
                        @endif

                        <!-- payment inputs -->
                        <div class="form-group">
                            <label>Payment</label>
                            <input type="number" required
                                wire:model="pay_money"
                                name="paid_amount"
                                id="paid_amount"
                                class="form-control"
                                step="0.01"
                                min="0"
                                @if(in_array($payment_method, ['bank transfer', 'credit card' ])) readonly @endif>
                        </div>
                        <div class="form-group">
                            <label>Returning Change</label>
                            <input type="number" wire:model="balance" readonly name="balance" id="balance" class="form-control">
                        </div>

                        <input type="hidden" name="payment_method" value="{{ $payment_method }}">
                        <!-- save button -->
                        <button class="btn btn-primary btn-lg btn-block mt-3">Save</button>
                    </div>
                </div>
            </form>
        </div> <!-- col-md-4 -->
    </div> <!-- row -->
</div> <!-- col-lg-12 -->