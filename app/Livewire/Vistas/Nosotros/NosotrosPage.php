<?php

namespace App\Livewire\Vistas\Nosotros;

use Livewire\Component;
use App\Models\Nosotros;
use App\Models\Banner;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class NosotrosPage extends Component
{
    public $nosotros;
    public $banner;

    public function mount()
    {
        $this->nosotros = Nosotros::first();
        $this->banner = Banner::where('section', 'nosotros')->first();
    }

    public function render()
    {
        return view('livewire.vistas.nosotros.nosotros', [
            'nosotros' => $this->nosotros,
            'banner' => $this->banner,
        ]);
    }
}