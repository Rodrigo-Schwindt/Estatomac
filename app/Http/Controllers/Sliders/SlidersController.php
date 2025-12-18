<?php

namespace App\Http\Controllers\Sliders;

use App\Models\Sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class SlidersController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $search = request('search', '');
        $sortField = request('sortField', 'orden');
        $sortDirection = request('sortDirection', 'asc');

        $sliders = Sliders::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('orden', 'like', "%{$search}%");
            });
        })
        ->orderBy($sortField, $sortDirection)
        ->paginate(5, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('livewire.sliders.partials.table', [
                    'sliders' => $sliders
                ])->render(),
                'pagination' => view('livewire.sliders.partials.pagination', [
                    'sliders' => $sliders
                ])->render()
            ]);
        }

        return view('livewire.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('livewire.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'orden' => 'required|integer|unique:sliders',
            'image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,ogg,mov,avi|max:102400',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        Sliders::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Slider creado correctamente',
            'redirect' => route('sliders.index')
        ]);
    }

    public function edit($id)
    {
        $slider = Sliders::findOrFail($id);
        return view('livewire.sliders.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $slider = Sliders::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'orden' => 'required|integer|unique:sliders,orden,' . $slider->id,
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,ogg,mov,avi|max:102400',
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Slider actualizado correctamente',
            'redirect' => route('sliders.index')
        ]);
    }

    public function destroy($id)
    {
        $slider = Sliders::findOrFail($id);

        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Slider eliminado correctamente'
            ]);
        }

        return redirect()->route('sliders.index')
            ->with('success', 'Slider eliminado correctamente');
    }
}