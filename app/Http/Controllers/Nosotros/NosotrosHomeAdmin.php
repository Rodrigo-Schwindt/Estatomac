<?php

namespace App\Http\Controllers\Nosotros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nosotros;
use Illuminate\Support\Facades\Storage;

class NosotrosHomeAdmin extends Controller
{
    public function index()
    {
        $nosotros = Nosotros::first();
        

        $previews = [
            'image_home' => $nosotros && $nosotros->image_home
                ? asset('storage/' . $nosotros->image_home)
                : null,
        ];

        return view('livewire.nosotros.nosotros-inicio', compact('nosotros', 'previews'));
    }

    public function save(Request $request)
{
    $request->validate([
        'title_home'       => 'nullable|string|max:255',
        'description_home' => 'nullable|string|max:5000',
        'image_home'       => 'nullable|image|max:4096',

    ]);

    $nosotros = Nosotros::first(); 

    $data = [
        'title_home'       => $request->title_home,
        'description_home' => $request->description_home,
    ];

    if ($request->hasFile('image_home')) {
        if ($nosotros && $nosotros->image_home) {
            Storage::disk('public')->delete($nosotros->image_home);
        }

        $data['image_home'] = $request->file('image_home')->store('nosotros', 'public');
    }

    if ($nosotros) {
        $nosotros->update($data);
    } else {
        $nosotros = Nosotros::create($data);
    }



    return redirect()
        ->route('nosotros.home.index')
        ->with('success', 'Cambios guardados correctamente');
}

    public function deleteImage()
    {
        $nosotros = Nosotros::first();

        if (!$nosotros || !$nosotros->image_home) {
            return response()->json(['success' => false], 404);
        }

        Storage::disk('public')->delete($nosotros->image_home);
        $nosotros->image_home = null;
        $nosotros->save();

        return response()->json(['success' => true]);
    }
}