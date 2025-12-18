<?php

namespace App\Http\Controllers\Sliders;

use App\Models\Sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SlidersCreate extends Controller
{
    public function index()
    {
        return view('livewire.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,webm,ogg,mov,avi|max:102400',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'orden' => 'nullable|string|max:10|unique:sliders',
            'url' => 'nullable|url|max:5000',  
        ]);

        try {
            if (!$request->hasFile('image')) {
                return response()->json([
                    'success' => false,
                    'errors' => ['image' => 'El archivo no se seleccionó correctamente']
                ], 422);
            }

            $file = $request->file('image');

            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['image' => 'El archivo no es válido']
                ], 422);
            }

            $imagePath = $file->store('sliders', 'public');

            $orden = $validated['orden'] ?? $this->getNextAvailableOrder();

            Sliders::create([
                'image' => $imagePath,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'orden' => strtoupper($orden),
                'url' => $validated['url'] ?? null, 
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slider creado correctamente',
                'redirect' => route('sliders.index')
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear slider: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el slider: ' . $e->getMessage()
            ], 500);
        }
    }


    private function getNextAvailableOrder()
    {
        $existingOrders = Sliders::orderBy('orden', 'asc')
            ->pluck('orden')
            ->map(function($orden) {
                return strtolower($orden);
            })
            ->toArray();

        if (empty($existingOrders)) {
            return 'aa';
        }

        $currentOrder = 'aa';
        while (in_array($currentOrder, $existingOrders)) {
            $currentOrder = $this->incrementOrder($currentOrder);
        }

        return $currentOrder;
    }


    private function incrementOrder($order)
    {
        $order = strtolower($order);
        $length = strlen($order);
        $chars = str_split($order);
        
        for ($i = $length - 1; $i >= 0; $i--) {
            if ($chars[$i] !== 'z') {
                $chars[$i] = chr(ord($chars[$i]) + 1);
                return implode('', $chars);
            } else {
                $chars[$i] = 'a';
            }
        }
        
        return str_repeat('a', $length + 1);
    }
}
