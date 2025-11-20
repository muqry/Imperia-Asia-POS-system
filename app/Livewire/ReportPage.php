<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Order_Detail;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ReportPage extends Component
{

    public $selectedDate;
public $availableDates = [];

public function mount()
{
    $this->selectedDate = now()->format('Y-m-d'); // default to today
    $this->availableDates = Order::selectRaw('DATE(created_at) as date')
        ->distinct()
        ->orderByDesc('date')
        ->pluck('date')
        ->toArray();
}



    public function render()
    {
        return view('livewire.report-page', [
        'orders' => $this->orders,
        ]);
    }


    public $filter = 'daily'; // daily or 'monthly', 'yearly'

    public function getOrdersProperty()
    {
        $query = Order::with(['details.product']);

        if ($this->filter === 'daily') {
            return $query->orderBy('created_at', 'desc')->get();
        }

        if ($this->filter === 'monthly') {
            return $query->whereYear('created_at', now()->year)->get();
        }

        if ($this->filter === 'yearly') {
            return $query->whereYear('created_at', now()->year)->get();
        }

        return collect(); // fallback if filter is invalid
    }
}
