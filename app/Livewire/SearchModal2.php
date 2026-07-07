<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;

class SearchModal2 extends Component
{
    public bool $isOpen = false;
    public string $search = '';
    public bool $isSearching = false;
    public $results;

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
        
        $this->results = Producto::with(['categoria'])
            ->where('visible', true)
            ->where(function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhereHas('categoria', function($subQuery) {
                          $subQuery->where('title', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('destacado', 'desc')
            ->orderBy('order', 'asc')
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