<?php

namespace App\Livewire\Vistas\Novedades;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Novedades;
use App\Models\NovCategories;
use Livewire\Attributes\Layout;

#[Layout('layouts.public2')]
class NovedadDetalle extends Component
{
    use WithPagination;

    public $id;
    public $novedad;
    public $search = '';
    public $category = null;
    public $month = null;
    public $showDetail = true;

    protected $queryString = [
        'search'   => ['except' => ''],
        'category' => ['except' => null],
        'month'    => ['except' => null],
    ];

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->id = $id;
        $this->novedad = Novedades::with('novcategories')->findOrFail($id);
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->showDetail = false;
    }

    public function updatedCategory()
    {
        $this->resetPage();
        $this->showDetail = false;
    }

    public function updatedMonth()
    {
        $this->resetPage();
        $this->showDetail = false;
    }

    public function clearCategory()
    {
        $this->category = null;
        $this->showDetail = false;
    }

    public function clearMonth()
    {
        $this->month = null;
        $this->showDetail = false;
    }

    public function paginationView()
    {
        return 'components.pagination.novedades';
    }

    public function render()
    {
        $categories = NovCategories::withCount('novedades')
            ->ordered()
            ->get();

        $archive = Novedades::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $novedades = null;
        if (!$this->showDetail) {
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
        }

        return view('livewire.vistas.novedades.novedad-detalle', [
            'categories' => $categories,
            'archive'    => $archive,
            'novedades'  => $novedades,
        ]);
    }
}