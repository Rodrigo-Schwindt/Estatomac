<?php

namespace App\Http\Controllers\Novedades;

use App\Http\Controllers\Controller;
use App\Models\NovCategories;
use Illuminate\Http\Request;

class NovCategoriesCreate extends Controller
{
    public function create()
    {
        return view('livewire.nov-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'orden' => 'nullable|string|max:10',
        ]);

        NovCategories::create([
            'title' => $request->title,
            'orden' => $request->orden,
        ]);

        return redirect()->route('novcategories.index')
            ->with('success', 'Novedad creada correctamente');
    }
}
