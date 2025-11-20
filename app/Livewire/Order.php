<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order_Detail;
use App\Models\Cart;

class Order extends Component
{
    public $orders, $products = [], $product_code, $message = '', $productIncart;

    public $pay_money = '', $balance = '';

    public $discounts = [];

    public $payment_method = 'cash';



    public function mount()
    {
        $this->products = Product::all();
        $this->productIncart = Cart::all();

        // initialize discounts for each cart row only if not already set
        foreach ($this->productIncart as $cart) {
            if (! isset($this->discounts[$cart->id])) {
                $this->discounts[$cart->id] = 0;
            }
        }
    }

    public function InsertoCart()
    {
        $countProduct = Product::where('id', $this->product_code)->first();

        if (!$countProduct) {
            return session()->flash('error', 'Product NOT Found');
        }

        $countCartProduct = Cart::where('product_id', $this->product_code)
            ->where('user_id', auth()->id())
            ->count();

        if ($countCartProduct > 0) {
            return session()->flash('error', 'Product ' . $countProduct->product_name . ' Already Exist in Cart, Please Add Quantity');
        }
        $add_to_cart = new Cart;
        $add_to_cart->product_id = $countProduct->id;
        $add_to_cart->product_qty = 1;
        $add_to_cart->product_price = $countProduct->price;
        $add_to_cart->user_id = auth()->user()->id;
        $add_to_cart->save();

        $this->productIncart->prepend($add_to_cart);

        $this->productIncart = Cart::all(); // refresh cart
        $this->refreshTotals(); // 👈 sync pay_money

        $this->product_code = '';
        return session()->flash('success', "Product Added Successfully!");
    }


    //IncrementQty
    public function IncrementQty($cartId)
    {
        $cart = Cart::find($cartId);
        $product = $cart->product;

        // Check if adding one more exceeds available stock
        if ($cart->product_qty + 1 > $product->quantity) {
            return session()->flash('error', 'Cannot add more than available stock (' . $product->quantity . ') for ' . $product->product_name);
        }

        $cart->increment('product_qty', 1);
        $cart->update(['product_price' => $cart->product_qty * $product->price]);
        $this->mount();
        $this->productIncart = Cart::all(); // refresh cart
        $this->refreshTotals(); // 👈 sync pay_money

    }


    // Decrement Quantity
    public function DecrementQty($cartId)
    {

        $carts = Cart::find($cartId);
        if ($carts->product_qty == 1) {
            return session()->flash('info', 'Product ' . $carts->product->product_name . ' Quantity can not be less than 1, add quantity or remove product in cart');
        }
        $carts->decrement('product_qty', 1);
        $updatePrice = $carts->product_qty * $carts->product->price;
        $carts->update(['product_price' => $updatePrice]);
        $this->mount();
        $this->productIncart = Cart::all(); // refresh cart
        $this->refreshTotals(); // 👈 sync pay_money

    }


    public function removeProduct($cartId)
    {
        $deleteCart = Cart::find($cartId);
        $deleteCart->delete();

        session()->flash('success', "Product REMOVED From The Cart");

        $this->productIncart = $this->productIncart->except($cartId);
        $this->productIncart = Cart::all(); // refresh cart
        $this->refreshTotals(); // 👈 sync pay_money

    }


    public function grandTotal(): float
    {
        $grandTotal = 0;

        foreach ($this->productIncart as $cart) {
            $discount = isset($this->discounts[$cart->id]) ? floatval($this->discounts[$cart->id]) : 0;
            $price = floatval($cart->product->price);
            $qty = intval($cart->product_qty);

            $finalUnit = $price - ($price * $discount / 100);
            $grandTotal += $finalUnit * $qty;
        }

        return round($grandTotal, 2);
    }

    public function getGrandTotalProperty()
    {
        $total = 0;

        foreach ($this->productIncart as $cart) {
            $disc = isset($this->discounts[$cart->id]) ? floatval($this->discounts[$cart->id]) : 0;
            $unit = floatval($cart->product->price);
            $qty = intval($cart->product_qty);

            $finalUnit = $unit - ($unit * $disc / 100);
            $total += $finalUnit * $qty;
        }

        return $this->grandTotal();
    }


    public function updated($property)
    {
        // Sync when payment method changes
        if ($property === 'payment_method') {
            if (in_array($this->payment_method, ['bank transfer', 'credit card'])) {
                $this->pay_money = $this->grandTotal;
            }
        }

        // Sync when cart or discounts change
        if (
            in_array($this->payment_method, ['bank transfer', 'credit card']) &&
            (
                str_starts_with($property, 'productIncart') ||
                str_starts_with($property, 'discounts')
            )
        ) {
            $this->pay_money = $this->grandTotal;
        }
    }


    public function refreshTotals()
    {
        // Recalculate pay_money if bank/card is selected
        if (in_array($this->payment_method, ['bank transfer', 'credit card'])) {
            $this->pay_money = $this->grandTotal;
        }
    }


    public function applyDefaultDiscount($value)
    {
        $value = floatval($value); // ensure it's numeric

        foreach ($this->productIncart as $cart) {
            $cart->discount = $value;
            $cart->save(); // persist to DB
            $this->discounts[$cart->id] = $value;
        }

        $this->refreshTotals();
        session()->flash('success', "Applied {$value}% discount to all products.");
    }

    public function saveDiscount($cartId)
    {
        $discount = $this->discounts[$cartId] ?? 0;

        $cart = Cart::find($cartId);
        if ($cart) {
            $cart->discount = $discount;
            $cart->save();
        }
    }







    public function render()
    {
        if (is_numeric($this->pay_money)) {
            $this->balance = round($this->pay_money - $this->grandTotal, 2);
        } else {
            $this->balance = 0.00;
        }

        return view('livewire.order', [
            'products' => $this->products,
            'grandTotal' => $this->grandTotal
        ]);
    }


    public function details()
    {
        return $this->hasMany(Order_Detail::class, 'order_id');
    }
}
