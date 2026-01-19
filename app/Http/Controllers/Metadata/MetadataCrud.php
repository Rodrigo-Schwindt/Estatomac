<?php

namespace App\Http\Controllers\Metadata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Metadata;
use App\Models\Product;
use App\Models\Category;
use App\Models\InfoTecnica;
use App\Models\Catalogo;
use App\Models\Novedades;

class MetadataCrud extends Controller
{
    /**
     * LISTADO + FILTRO
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // ORDEN REAL DE TU WEB
        $sectionOrder = [
            'home'              => 1,
            'nosotros'          => 2,
            'categorias'        => 3,
            'info-tecnica'      => 4,
            'catalogos'         => 5,
            'novedades'         => 6,
            'contacto'          => 7,
        ];

        $case = 'CASE ';
        foreach ($sectionOrder as $sec => $ord) {
            $case .= "WHEN section = '$sec' THEN $ord ";
        }
        $case .= 'ELSE 999 END';

        $query = Metadata::query();

        // BÚSQUEDA
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

        return view('livewire.metadata.crud', [
            'items'    => $items,
            'search'   => $search,

            // SECCIONES ESTÁTICAS REALES
            'sections' => [
                'home',
                'nosotros',
                'categorias',
                'info-tecnica',
                'catalogos',
                'novedades',
                'contacto',
            ],

        ]);
    }

    /**
     * GUARDAR / EDITAR METADATA
     */
    public function save(Request $request)
    {
        $metadataType = $request->metadataType; // section | producto | categoria | info-tecnica | catalogo | novedad
        $metadataId   = $request->metadataId;

        // Si NO es sección padre → genero section dinámica
        if ($metadataType !== 'section') {

            if (!$request->itemId) {
                return back()->withErrors(['itemId' => 'Debes seleccionar un elemento.'])->withInput();
            }

            $map = [
                'producto'      => 'producto-'.$request->itemId,
                'categoria'     => 'categoria-'.$request->itemId,
                'info-tecnica'  => 'info-tecnica-'.$request->itemId,
                'catalogo'      => 'catalogo-'.$request->itemId,
                'novedad'       => 'novedad-'.$request->itemId,
            ];

            $request->merge(['section' => $map[$metadataType]]);
        }

        $rules = [
            'section'     => 'required|string|max:255|unique:metadata,section,' . ($metadataId ?? 'NULL'),
            'keywords'    => 'required|string',
            'description' => 'required|string|max:160',
            'metadataType'=> 'required|in:section,producto,categoria,info-tecnica,catalogo,novedad',
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

    /**
     * ELIMINAR
     */
    public function delete($id)
    {
        Metadata::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}