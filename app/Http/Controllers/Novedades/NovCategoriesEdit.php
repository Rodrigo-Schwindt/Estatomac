<?php

namespace App\Http\Controllers\Novedades;

use App\Http\Controllers\Controller;
use App\Models\NovCategories;
use Illuminate\Http\Request;

class NovCategoriesEdit extends Controller
{
    public function edit($id)
    {
        $category = NovCategories::findOrFail($id);

        return view('livewire.nov-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'orden' => 'nullable|string|max:10',
        ]);

        $category = NovCategories::findOrFail($id);

        $category->update([
            'title' => $request->title,
            'orden' => $request->orden,
        ]);

        return redirect()->route('novcategories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }
}
