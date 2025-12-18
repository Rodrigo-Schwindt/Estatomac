<?php

namespace App\Http\Controllers\Sliders;

use App\Models\Sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class SlidersEdit extends Controller
{
    public function index($id)
    {
        $slider = Sliders::findOrFail($id);
        return view('livewire.sliders.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $slider = Sliders::findOrFail($id);

        $validated = $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,webm,ogg,mov,avi|max:102400',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'orden' => 'nullable|string|max:10|unique:sliders,orden,' . $slider->id,
            'url' => 'nullable|url|max:500',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                    Storage::disk('public')->delete($slider->image);
                }
                
                $validated['image'] = $request->file('image')->store('sliders', 'public');
            }

            $slider->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'orden' => strtoupper($validated['orden']),
                'image' => $validated['image'] ?? $slider->image,
                'url' => $validated['url'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slider actualizado correctamente',
                'redirect' => route('sliders.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el slider: ' . $e->getMessage()
            ], 500);
        }
    }
}