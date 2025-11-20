<?php

namespace App\Livewire;
use App\Models\Product;
use Livewire\Component;
//use Livewire\WithPagination;


class Products extends Component
{
    //use WithPagination;
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
         $products = Product::all(); // Fetch all products without pagination
    return view('livewire.products', ['products' => $products]);
    }

}
