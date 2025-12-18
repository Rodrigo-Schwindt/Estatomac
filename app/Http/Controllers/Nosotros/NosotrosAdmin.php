<?php

namespace App\Http\Controllers\Nosotros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nosotros;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class NosotrosAdmin extends Controller
{
    public function index()
    {
        $nosotros = Nosotros::first();
        $banner = Banner::where('section', 'nosotros')->first();

        return view('livewire.nosotros.admin', [
            'nosotros' => $nosotros,
            'banner' => $banner,
        ]);
    }

    public function save(Request $request)
    {
        $nosotros = Nosotros::first();

        $validated = $request->validate([
            'title'         => 'nullable|string|max:255',
            'description'   => 'nullable|string',

            'title_1'       => 'nullable|string|max:255',
            'description_1' => 'nullable|string',

            'title_2'       => 'nullable|string|max:255',
            'description_2' => 'nullable|string',

            'title_3'       => 'nullable|string|max:255',
            'description_3' => 'nullable|string',
            
            'title_4'       => 'nullable|string|max:255',
            'description_4' => 'nullable|string',

            'image'   => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:3072',
            'image_1' => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:3072',
            'image_2' => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:3072',
            'image_3' => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:3072',
            'image_4' => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:3072',

            'banner_title' => 'nullable|string|max:255',
            'banner_image' => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:5072',
        ]);

        $data = [
            'title'         => $validated['title']         ?? null,
            'description'   => $validated['description']   ?? null,        

            'title_1'       => $validated['title_1']       ?? null,
            'description_1' => $validated['description_1'] ?? null,

            'title_2'       => $validated['title_2']       ?? null,
            'description_2' => $validated['description_2'] ?? null,

            'title_3'       => $validated['title_3']       ?? null,
            'description_3' => $validated['description_3'] ?? null,

            'title_4'       => $validated['title_4']       ?? null,
            'description_4' => $validated['description_4'] ?? null,
        ];

        $imageFields = [
            'image',
            'image_1',
            'image_2',
            'image_3',
            'image_4',
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                if ($nosotros && $nosotros->{$field}) {
                    Storage::disk('public')->delete($nosotros->{$field});
                }
                $data[$field] = $request->file($field)->store('nosotros', 'public');
            } else {
                if ($nosotros) {
                    $data[$field] = $nosotros->{$field};
                }
            }
        }

        if ($nosotros) {
            $nosotros->update($data);
        } else {
            $nosotros = Nosotros::create($data);
        }

        $banner = Banner::firstOrNew(['section' => 'nosotros']);
        $banner->title = $validated['banner_title'] ?? null;

        if ($request->hasFile('banner_image')) {
            if ($banner->image_banner) {
                Storage::disk('public')->delete($banner->image_banner);
            }
            $banner->image_banner = $request->file('banner_image')->store('banners', 'public');
        }

        $banner->save();

        return redirect()->route('nosotros.index')->with('success', 'Datos actualizados correctamente');
    }

    public function deleteImage($field)
    {
        $allowed = [
            'image_home',
            'image',
            'image_1',
            'image_2',
            'image_3',
            'image_4',
            'banner_image',
        ];

        if (!in_array($field, $allowed, true)) {
            abort(404);
        }

        if ($field === 'banner_image') {
            $banner = Banner::where('section', 'nosotros')->first();
            
            if (!$banner || !$banner->image_banner) {
                return response()->json(['success' => false], 404);
            }

            Storage::disk('public')->delete($banner->image_banner);
            $banner->image_banner = null;
            $banner->save();

            return response()->json(['success' => true]);
        }

        $nosotros = Nosotros::first();

        if (!$nosotros || !$nosotros->{$field}) {
            return response()->json(['success' => false], 404);
        }

        Storage::disk('public')->delete($nosotros->{$field});
        $nosotros->{$field} = null;
        $nosotros->save();

        return response()->json(['success' => true]);
    }
}
