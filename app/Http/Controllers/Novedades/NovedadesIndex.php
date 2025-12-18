<?php

namespace App\Http\Controllers\Novedades;

use App\Http\Controllers\Controller;
use App\Models\Novedades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NovedadesIndex extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $banner = Novedades::orderBy('id', 'asc')->first();
        if (!$banner) {
            $banner = Novedades::create([
                'title' => 'Banner global',
                'description' => '',
                'orden' => '0',
                'destacado' => false,
            ]);
        }

        $query = Novedades::where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('orden', 'asc');

        $novedades = $query->paginate(12);

        if ($request->ajax()) {
            if ($novedades->currentPage() > $novedades->lastPage() && $novedades->lastPage() > 0) {
                $novedades = $query->paginate(12, ['*'], 'page', $novedades->lastPage());
            }

            $html = view('livewire.novedades.index', [
                'banner'    => $banner,
                'novedades' => $novedades,
                'search'    => $search,
            ])->render();

            $htmlSection = $this->extractAjaxSection($html);

            return response()->json([
                'html'  => $htmlSection,
                'total' => $novedades->total(),
                'pages' => $novedades->lastPage(),
            ]);
        }

        return view('livewire.novedades.index', compact('banner', 'novedades', 'search'));
    }

    protected function extractAjaxSection(string $html): string
    {
        $startDiv = strpos($html, '<div id="ajax-wrapper">');
        if ($startDiv === false) {
            return '';
        }

        $startContent = strpos($html, '>', $startDiv);
        if ($startContent === false) {
            return '';
        }
        $startContent++;

        $endMarker = strpos($html, '<!-- END_AJAX_WRAPPER -->', $startContent);
        if ($endMarker === false) {
            return '';
        }

        return substr($html, $startContent, $endMarker - $startContent);
    }

    public function delete(Request $request, $id)
    {
        $nov = Novedades::find($id);

        if (!$nov) {
            return response()->json(['error' => 'No encontrada'], 404);
        }

        $nov->delete();

        return response()->json(['success' => 'Novedad eliminada']);
    }

    public function toggleDestacado(Request $request, $id)
    {
        $nov = Novedades::find($id);

        if (!$nov) {
            return response()->json(['error' => 'No encontrada'], 404);
        }

        $nov->destacado = !$nov->destacado;
        $nov->save();

        return response()->json(['success' => 'Estado actualizado']);
    }

    public function saveBanner(Request $request)
    {
        $request->validate([
            'image_banner' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
        ]);

        $banner = Novedades::orderBy('id', 'asc')->first();
        if (!$banner) {
            $banner = Novedades::create([
                'title' => 'Banner global',
                'description' => '',
                'orden' => '0',
                'destacado' => false,
            ]);
        }

        if ($banner->image_banner && Storage::disk('public')->exists($banner->image_banner)) {
            Storage::disk('public')->delete($banner->image_banner);
        }

        $path = $request->file('image_banner')->store('novedades/banners', 'public');

        $banner->image_banner = $path;
        $banner->save();

        return response()->json([
            'success' => 'Banner actualizado',
            'url'     => Storage::url($banner->image_banner),
        ]);
    }

    public function removeBanner(Request $request)
    {
        $banner = Novedades::orderBy('id', 'asc')->first();

        if (!$banner || !$banner->image_banner) {
            return response()->json(['error' => 'No hay banner'], 404);
        }

        if (Storage::disk('public')->exists($banner->image_banner)) {
            Storage::disk('public')->delete($banner->image_banner);
        }

        $banner->image_banner = null;
        $banner->save();

        return response()->json([
            'success' => 'Banner eliminado',
        ]);
    }
}
