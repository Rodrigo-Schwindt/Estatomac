<?php

namespace App\Http\Controllers\Novedades;

use App\Http\Controllers\Controller;
use App\Models\Novedades;
use App\Models\NovCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NovedadesCreate extends Controller
{
    public function create()
    {
        $categories = NovCategories::ordered()->get();
        return view('livewire.novedades.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'orden'               => 'nullable|string|max:10',
            'destacado'           => 'nullable', // Cambiar de 'boolean' a 'nullable'
            'image'               => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'image_banner'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'selectedCategories'   => 'nullable|array', // Agregar 'nullable'
            'selectedCategories.*' => 'exists:nov_categories,id',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'orden'       => $request->orden,
            'destacado'   => $request->has('destacado') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('novedades', 'public');
        }

        if ($request->hasFile('image_banner')) {
            $data['image_banner'] = $request->file('image_banner')->store('novedades/banners', 'public');
        }

        $novedad = Novedades::create($data);

        if ($request->filled('selectedCategories')) {
            $novedad->novcategories()->sync($request->selectedCategories);
        }

        return redirect()
            ->route('novedades.index')
            ->with('toast', [
                'message' => 'Novedad creada correctamente',
                'type' => 'success'
            ]);
    }
}