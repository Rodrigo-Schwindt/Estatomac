<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Collection;

class SearchModal2 extends Component
{
    public bool $isOpen = false;
    public string $search = '';
    public bool $isSearching = false;
    public Collection $results;

    protected $listeners = ['close-search' => 'closeSearch'];

    public function mount()
    {
        $this->results = collect();
    }

    public function updatedSearch()
{
    if (strlen($this->search) < 2) {
        $this->results = collect();
        $this->isSearching = false;
        return;
    }

    $this->isSearching = true;
    
    $this->results = Product::with(['category', 'images'])
        ->where('visible', true)
        ->where(function($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('ficha_tecnica', 'like', '%' . $this->search . '%');
        })
        ->orWhereHas('category', function($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->where('visible', true);
        })
        ->orderBy('destacado', 'desc')
        ->orderBy('orden', 'asc')
        ->orderBy('title', 'asc')
        ->limit(10)
        ->get();

    $this->isSearching = false;
}

    public function toggleSearch()
    {
        $this->isOpen = !$this->isOpen;
        
        if ($this->isOpen) {
            $this->dispatch('search-opened');
        } else {
            $this->search = '';
            $this->results = collect();
        }
    }

    public function render()
    {
        return view('livewire.search-modal2');
    }
}