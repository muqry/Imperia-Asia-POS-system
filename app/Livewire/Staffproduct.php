<?php

namespace App\Livewire;
use App\Models\Product;
use Livewire\Component;

class Staffproduct extends Component

{
    public $products_detail = [];


    public function mount() 
    {
        
    }

    public function ProductDetails($product_id)
    {
        $this->products_detail = Product::where('id',$product_id)->get();
    }

    public function render()
    {
        $products = Product::all();
        return view('livewire.staffproduct', ['products' => $products])
            ->layout('layouts.app'); // ✅ This triggers Livewire to inject $slot
    }
}
