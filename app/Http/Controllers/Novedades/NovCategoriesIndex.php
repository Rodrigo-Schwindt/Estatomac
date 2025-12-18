<?php

namespace App\Http\Controllers\Novedades;

use App\Http\Controllers\Controller;
use App\Models\NovCategories;
use Illuminate\Http\Request;

class NovCategoriesIndex extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $categories = NovCategories::ordered()
            ->where('title', 'like', "%{$search}%")
            ->paginate(12);

        if ($request->ajax()) {

            if ($categories->currentPage() > $categories->lastPage() && $categories->lastPage() > 0) {
                $categories = NovCategories::ordered()
                    ->where('title', 'like', "%{$search}%")
                    ->paginate(12, ['*'], 'page', $categories->lastPage());
            }

            $html = view('livewire.nov-categories.index', [
                'categories' => $categories,
                'search' => $search
            ])->render();

            $html = $this->extractAjaxSection($html);

            return response()->json([
                'html' => $html,
                'total' => $categories->total(),
                'pages' => $categories->lastPage(),
            ]);
        }

        return view('livewire.nov-categories.index', compact('categories', 'search'));
    }

    private function extractAjaxSection($html)
    {
        $start = strpos($html, '<div id="ajax-wrapper">');
        $end = strpos($html, '</div>', $start) + 6;
        return substr($html, $start, $end - $start);
    }

    public function delete(Request $request, $id)
    {
        $category = NovCategories::find($id);

        if (!$category) {
            return response()->json(['error' => 'No encontrada'], 404);
        }

        $category->delete();

        return response()->json(['success' => 'Categoría eliminada']);
    }
}
