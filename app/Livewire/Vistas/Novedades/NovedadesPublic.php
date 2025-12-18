<?php

namespace App\Livewire\Vistas\Novedades;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Novedades;
use App\Models\NovCategories;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class NovedadesPublic extends Component
{
    use WithPagination;



    public $search = '';
    public $category = null;
    public $month = null; // formato "YYYY-MM"

    protected $queryString = [
        'search'   => ['except' => ''],
        'category' => ['except' => null],
        'month'    => ['except' => null],
    ];

    protected $paginationTheme = 'tailwind';

    public function updatedSearch()
    {
        $this->resetPage();
    
    }

    public function updatedCategory()
    {
        $this->resetPage();
     
    }

    public function updatedMonth()
    {
        $this->resetPage();
   
    }

    public function updatedPage()
    {
        $this->dispatch('novedades-scroll');
    }

    public function clearCategory()
    {
        $this->category = null;
     
    }

    public function clearMonth()
    {
        $this->month = null;
       
    }

    public function paginationView()
    {
        return 'components.pagination.novedades';
    }

    public function render()
    {
        $destacadas = Novedades::where('destacado', true)
            ->ordered()
            ->get();

        $query = Novedades::query()
            ->search($this->search)
            ->when($this->category, fn($q) => $q->inCategory($this->category))
            ->when($this->month, function ($q) {
                [$year, $month] = explode('-', $this->month);
                $q->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
            })
            ->orderBy('created_at', 'desc');

        $novedades = $query->paginate(4);

        $banner = Novedades::whereNotNull('image_banner')->first();

        $categories = NovCategories::withCount('novedades')
            ->ordered()
            ->get();

        $archive = Novedades::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('livewire.vistas.novedades.novedades-public', [
            'banner'     => $banner,
            'destacadas' => $destacadas,
            'novedades'  => $novedades,
            'categories' => $categories,
            'archive'    => $archive,
        ]);
    }
}
