<?php

namespace App\Http\Controllers\Novedades;

use App\Http\Controllers\Controller;
use App\Models\Novedades;
use App\Models\NovCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NovedadesEdit extends Controller
{
    public function edit($id)
    {
        $novedad = Novedades::with('novcategories')->findOrFail($id);
        $categories = NovCategories::ordered()->get();
        return view('livewire.novedades.edit', compact('novedad', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $novedad = Novedades::findOrFail($id);

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'orden'               => 'nullable|string|max:10',
            'destacado'           => 'nullable', // Cambiar de 'boolean' a 'nullable'
            'newImage'            => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'newImageBanner'      => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'selectedCategories'   => 'nullable|array', // Agregar 'nullable'
            'selectedCategories.*' => 'exists:nov_categories,id',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'orden'       => $request->orden,
            'destacado'   => $request->has('destacado') ? 1 : 0, // Conversión explícita
        ];

        if ($request->hasFile('newImage')) {
            if ($novedad->image && Storage::disk('public')->exists($novedad->image)) {
                Storage::disk('public')->delete($novedad->image);
            }
            $data['image'] = $request->file('newImage')->store('novedades', 'public');
        }

        if ($request->hasFile('newImageBanner')) {
            if ($novedad->image_banner && Storage::disk('public')->exists($novedad->image_banner)) {
                Storage::disk('public')->delete($novedad->image_banner);
            }
            $data['image_banner'] = $request->file('newImageBanner')->store('novedades/banners', 'public');
        }

        $novedad->update($data);

        // Sincronizar solo si hay categorías
        if ($request->filled('selectedCategories')) {
            $novedad->novcategories()->sync($request->selectedCategories);
        } else {
            $novedad->novcategories()->detach(); // Desvincular todas si no se seleccionó ninguna
        }

        return redirect()->route('novedades.index')->with('toast', [
            'message' => 'Novedad actualizada',
            'type' => 'success'
        ]);
    }
}