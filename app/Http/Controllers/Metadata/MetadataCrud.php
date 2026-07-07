<?php

namespace App\Http\Controllers\Metadata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Metadata;
use App\Models\ProductoTodotex;
use App\Models\CategoriaTodotex;
use App\Models\Novedades;

class MetadataCrud extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $sectionOrder = [
            'home'       => 1,
            'nosotros'   => 2,
            'productos'  => 3,
            'novedades'  => 4,
            'contacto'   => 5,
        ];

        $case = 'CASE ';
        foreach ($sectionOrder as $sec => $ord) {
            $case .= "WHEN section = '$sec' THEN $ord ";
        }
        $case .= 'ELSE 999 END';

        $query = Metadata::query();

        if ($search) {
            $like = "%$search%";
            $query->where(function ($q) use ($like) {
                $q->where('section', 'like', $like)
                  ->orWhere('keywords', 'like', $like)
                  ->orWhere('description', 'like', $like);
            });
        }

        $items = $query->orderByRaw($case)
                       ->orderBy('section')
                       ->paginate(10)
                       ->withQueryString();

        $productos = ProductoTodotex::orderBy('titulo')->get();
        $categories = CategoriaTodotex::orderBy('titulo')->get();
        $novedades = Novedades::orderBy('title')->get();

        return view('livewire.metadata.crud', [
            'items'      => $items,
            'search'     => $search,
            'sections'   => ['home', 'nosotros', 'productos', 'novedades', 'contacto'],
            'products'   => $productos,
            'categories' => $categories,
            'novedades'  => $novedades,
        ]);
    }

    public function save(Request $request)
    {
        $metadataType = $request->metadataType;
        $metadataId   = $request->metadataId;

        if ($metadataType !== 'section') {
            if (!$request->itemId) {
                return back()->withErrors(['itemId' => 'Debes seleccionar un elemento.'])->withInput();
            }

            $map = [
                'producto'  => 'producto-'.$request->itemId,
                'categoria' => 'categoria-'.$request->itemId,
                'novedad'   => 'novedad-'.$request->itemId,
            ];

            $request->merge(['section' => $map[$metadataType]]);
        }

        $rules = [
            'section'      => 'required|string|max:255|unique:metadata,section,' . ($metadataId ?? 'NULL'),
            'keywords'     => 'required|string',
            'description'  => 'required|string|max:160',
            'metadataType' => 'required|in:section,producto,categoria,novedad',
        ];

        if ($metadataType !== 'section') {
            $rules['itemId'] = 'required|integer';
        }

        $data = $request->validate($rules);

        Metadata::updateOrCreate(
            ['id' => $metadataId],
            [
                'section'     => $data['section'],
                'keywords'    => $data['keywords'],
                'description' => $data['description'],
            ]
        );

        return redirect()->route('admin.metadata')
            ->with('success', 'Metadato guardado correctamente');
    }

    public function delete($id)
    {
        Metadata::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}