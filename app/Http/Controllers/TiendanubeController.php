<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Producto;
use App\Models\Subcategoria;
use App\Models\Gallery;
use Illuminate\Support\Facades\DB;

class TiendanubeController extends Controller
{
    /**
     * Maneja el callback de Tiendanube después de la autorización
     */
    public function handleCallback(Request $request)
    {
        // 1. Capturar el código que envía Tiendanube
        $code = $request->query('code');
        
        if (!$code) {
            return response()->json([
                'error' => 'No se recibió el código de autorización'
            ], 400);
        }

        try {
            // 2. Intercambiar el código por el Access Token
            $response = Http::post('https://www.tiendanube.com/apps/authorize/token', [
                'client_id' => config('services.tiendanube.client_id'),
                'client_secret' => config('services.tiendanube.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $code
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // 3. Guardar el token (podés guardarlo en BD, cache, etc.)
                $accessToken = $data['access_token'];
                $userId = $data['user_id']; // ID de la tienda
                
                // Por ahora lo guardamos en session para testing
                session([
                    'tiendanube_token' => $accessToken,
                    'tiendanube_user_id' => $userId
                ]);

                Log::info('Token de Tiendanube obtenido', [
                    'user_id' => $userId,
                    'token' => substr($accessToken, 0, 10) . '...' // Solo primeros caracteres por seguridad
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '¡Conexión exitosa con Tiendanube!',
                    'user_id' => $userId
                ]);

            } else {
                Log::error('Error al obtener token', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Error al conectar con Tiendanube',
                    'details' => $response->json()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Excepción en callback de Tiendanube', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error interno',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importCategory(Request $request)
    {
        $categoryName = $request->query('category', 'CARCASAS');
        $categoriaId = $request->query('categoria_id', 9);
        $nuevoValue = $request->query('nuevo', null);
        $importadosValue = $request->query('importados', false);
        
        $token = session('tiendanube_token');
        $userId = session('tiendanube_user_id');

        if (!$token) {
            return response()->json(['error' => 'No hay token'], 401);
        }

        try {
            // 1. Traer productos filtrados
            $page = 1;
            $allProducts = [];
            
            while (true) {
                $response = Http::withHeaders([
                    'Authentication' => 'bearer ' . $token,
                    'User-Agent' => config('services.tiendanube.user_agent')
                ])->get("https://api.tiendanube.com/v1/{$userId}/products", [
                    'page' => $page,
                    'per_page' => 50
                ]);

                if ($response->successful()) {
                    $products = $response->json();
                    if (empty($products)) break;
                    $allProducts = array_merge($allProducts, $products);
                    $page++;
                } else {
                    if ($response->status() === 404) break;
                    return response()->json(['error' => 'Error al traer productos'], $response->status());
                }
            }

            // 2. Filtrar por categoría
            $filteredProducts = array_filter($allProducts, function($product) use ($categoryName) {
                if (!empty($product['categories'])) {
                    foreach ($product['categories'] as $cat) {
                        if (stripos($cat['name']['es'], $categoryName) !== false) {
                            return true;
                        }
                    }
                }
                return false;
            });

            // 3. Importar cada producto
            $imported = 0;
            $errors = [];

            foreach ($filteredProducts as $item) {
                try {
                    $this->importProduct($item, $categoriaId, $nuevoValue, $importadosValue);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'product' => $item['name']['es'] ?? 'Sin nombre',
                        'error' => $e->getMessage()
                    ];
                    Log::error('Error importando producto', [
                        'product' => $item['name']['es'] ?? 'Sin nombre',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'total_tiendanube' => count($allProducts),
                'filtered' => count($filteredProducts),
                'imported' => $imported,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error en importación', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

private function importProduct($item, $categoriaId, $nuevoValue = null, $importadosValue = false)
{
    DB::beginTransaction();
    
    try {
        // ✅ Verificar duplicados por code O por title
        $code = $item['handle']['es'];
        $title = $item['name']['es'];
        $newPrice = $item['variants'][0]['price'] ?? 0;

        $existingProduct = Producto::where('code', $code)
                                    ->orWhere('title', $title)
                                    ->first();

        if ($existingProduct) {
            // ✅ Actualizar precio en vez de saltar
            $oldPrice = $existingProduct->precio;
            $existingProduct->update(['precio' => $newPrice]);
            
            Log::info('Producto existente - precio actualizado', [
                'code' => $code,
                'title' => $title,
                'old_price' => $oldPrice,
                'new_price' => $newPrice
            ]);
            
            DB::commit();
            return $existingProduct; // ✅ Retornar como éxito
        }

        // 1. Crear el producto
        $producto = Producto::create([
            'title' => $title,
            'code' => $code,
            'precio' => $newPrice,
            'visible' => $item['published'] ?? true,
            'categoria_id' => $categoriaId,
            'nuevo' => $nuevoValue ?? 'nuevo',
            'importados' => $importadosValue,
            'image' => null,
            'order' => 0,
            'destacado' => false,
            'oferta' => false,
        ]);

        // 2. Descargar y guardar imagen principal
        if (!empty($item['images'])) {
            $mainImage = $item['images'][0];
            $imagePath = $this->downloadImage($mainImage['src'], $producto->id);
            $producto->update(['image' => $imagePath]);
        }

        // 3. Guardar galería (imágenes adicionales)
        if (count($item['images']) > 1) {
            for ($i = 1; $i < count($item['images']); $i++) {
                $imageUrl = $item['images'][$i]['src'];
                $galleryPath = $this->downloadImage($imageUrl, $producto->id, true);
                
                Gallery::create([
                    'producto_id' => $producto->id,
                    'image' => $galleryPath,
                    'order' => $i
                ]);
            }
        }

        // 4. Crear subcategoría "Marca" con el valor de la marca
        if (!empty($item['brand'])) {
            $subcategoriaMarca = Subcategoria::firstOrCreate(
                ['title' => 'Marca', 'categoria_id' => $categoriaId],
                ['order' => 0, 'tolerancia' => 0]
            );

            $producto->subcategorias()->attach($subcategoriaMarca->id, [
                'valor' => $item['brand']
            ]);
        }

        // 5. Procesar attributes + values como subcategorías
        if (!empty($item['attributes']) && !empty($item['variants'][0]['values'])) {
            $attributes = $item['attributes'];
            $values = $item['variants'][0]['values'];

            for ($i = 0; $i < count($attributes); $i++) {
                if (isset($values[$i])) {
                    $attrName = $attributes[$i]['es'];
                    $attrValue = $values[$i]['es'];

                    // Buscar o crear subcategoría
                    $subcategoria = Subcategoria::firstOrCreate(
                        ['title' => $attrName, 'categoria_id' => $categoriaId],
                        ['order' => 0, 'tolerancia' => 0]
                    );

                    // Asociar con el producto y su valor
                    $producto->subcategorias()->attach($subcategoria->id, [
                        'valor' => $attrValue
                    ]);
                }
            }
        }

        DB::commit();
        return $producto;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
    private function downloadImage($url, $productoId, $isGallery = false)
    {
        try {
            // Crear directorio si no existe
            $directory = storage_path('app/public/productos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generar nombre único
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            $filename = $productoId . '_' . uniqid() . '.' . $extension;
            $filepath = $directory . '/' . $filename;

            // Descargar imagen
            $imageContent = file_get_contents($url);
            file_put_contents($filepath, $imageContent);

            // Retornar path relativo
            return 'productos/' . $filename;

        } catch (\Exception $e) {
            Log::error('Error descargando imagen', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function migrateCategory(Request $request)
    {
        $categoryName = $request->query('category', 'CARCASAS');
        $token = session('tiendanube_token');
        $userId = session('tiendanube_user_id');

        if (!$token) {
            return response()->json(['error' => 'No hay token. Primero autoriza la app.'], 401);
        }

        try {
            // 1. Traer TODOS los productos (paginado)
            $page = 1;
            $allProducts = [];
            
            while (true) {
                $response = Http::withHeaders([
                    'Authentication' => 'bearer ' . $token,
                    'User-Agent' => config('services.tiendanube.user_agent')
                ])->get("https://api.tiendanube.com/v1/{$userId}/products", [
                    'page' => $page,
                    'per_page' => 50
                ]);

                if ($response->successful()) {
                    $products = $response->json();
                    
                    // Si no hay productos, terminamos
                    if (empty($products)) {
                        break;
                    }
                    
                    $allProducts = array_merge($allProducts, $products);
                    $page++;
                    
                } else {
                    // Si es 404, terminamos (última página alcanzada)
                    if ($response->status() === 404) {
                        break;
                    }
                    
                    return response()->json([
                        'error' => 'Error al traer productos',
                        'details' => $response->json()
                    ], $response->status());
                }
            }

            // 2. Filtrar solo productos de la categoría deseada
            $filteredProducts = array_filter($allProducts, function($product) use ($categoryName) {
                if (!empty($product['categories'])) {
                    foreach ($product['categories'] as $cat) {
                        if (stripos($cat['name']['es'], $categoryName) !== false) {
                            return true;
                        }
                    }
                }
                return false;
            });

            return response()->json([
                'success' => true,
                'total_products' => count($allProducts),
                'category_filter' => $categoryName,
                'filtered_count' => count($filteredProducts),
                'products' => array_values($filteredProducts)
            ]);

        } catch (\Exception $e) {
            Log::error('Error en migración de categoría', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error interno',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importCategoryBatch(Request $request)
    {
        $categoryName = $request->query('category', 'CARCASAS');
        $categoriaId = $request->query('categoria_id', 9);
        $nuevoValue = $request->query('nuevo', null);
        $importadosValue = $request->query('importados', false); // ✅ Nuevo
        $batchSize = 20;
        
        $token = session('tiendanube_token');
        $userId = session('tiendanube_user_id');

        if (!$token) {
            return response()->json(['error' => 'No hay token'], 401);
        }

        try {
            // 1. Traer todos los productos
            $page = 1;
            $allProducts = [];
            
            while (true) {
                $response = Http::withHeaders([
                    'Authentication' => 'bearer ' . $token,
                    'User-Agent' => config('services.tiendanube.user_agent')
                ])->get("https://api.tiendanube.com/v1/{$userId}/products", [
                    'page' => $page,
                    'per_page' => 50
                ]);

                if ($response->successful()) {
                    $products = $response->json();
                    if (empty($products)) break;
                    $allProducts = array_merge($allProducts, $products);
                    $page++;
                } else {
                    if ($response->status() === 404) break;
                    return response()->json(['error' => 'Error al traer productos'], $response->status());
                }
            }

            // 2. Filtrar por categoría
            $filteredProducts = array_filter($allProducts, function($product) use ($categoryName) {
                if (!empty($product['categories'])) {
                    foreach ($product['categories'] as $cat) {
                        if (stripos($cat['name']['es'], $categoryName) !== false) {
                            return true;
                        }
                    }
                }
                return false;
            });

            $filteredProducts = array_values($filteredProducts);

            // 3. Guardar en sesión para procesamiento por lotes
            session([
                'import_batch_products' => $filteredProducts,
                'import_batch_categoria_id' => $categoriaId,
                'import_batch_nuevo' => $nuevoValue,
                'import_batch_importados' => $importadosValue, // ✅ Nuevo
                'import_batch_offset' => 0,
                'import_batch_total' => count($filteredProducts)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Importación iniciada',
                'total_products' => count($filteredProducts),
                'batch_size' => $batchSize,
                'batches_needed' => ceil(count($filteredProducts) / $batchSize)
            ]);

        } catch (\Exception $e) {
            Log::error('Error iniciando importación por lotes', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function processBatch(Request $request)
    {
        $batchSize = 20;
        
        $products = session('import_batch_products', []);
        $categoriaId = session('import_batch_categoria_id', 9);
        $nuevoValue = session('import_batch_nuevo', null);
        $importadosValue = session('import_batch_importados', false); // ✅ Nuevo
        $offset = session('import_batch_offset', 0);
        $total = session('import_batch_total', 0);

        if (empty($products)) {
            return response()->json([
                'success' => false,
                'message' => 'No hay productos para importar. Ejecutá primero /import-batch'
            ], 400);
        }

        // Tomar el siguiente lote
        $batch = array_slice($products, $offset, $batchSize);
        
        if (empty($batch)) {
            // Limpiar sesión
            session()->forget([
                'import_batch_products',
                'import_batch_categoria_id',
                'import_batch_nuevo',
                'import_batch_importados',
                'import_batch_offset',
                'import_batch_total'
            ]);

            return response()->json([
                'success' => true,
                'completed' => true,
                'message' => '¡Importación completada!',
                'total_imported' => $total
            ]);
        }

        // Importar el lote
        $imported = 0;
        $errors = [];

        foreach ($batch as $item) {
            try {
                $this->importProduct($item, $categoriaId, $nuevoValue, $importadosValue); // ✅ Pasar parámetro
                $imported++;
            } catch (\Exception $e) {
                $errors[] = [
                    'product' => $item['name']['es'] ?? 'Sin nombre',
                    'error' => $e->getMessage()
                ];
                Log::error('Error importando producto', [
                    'product' => $item['name']['es'] ?? 'Sin nombre',
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Actualizar offset
        $newOffset = $offset + $batchSize;
        session(['import_batch_offset' => $newOffset]);

        return response()->json([
            'success' => true,
            'completed' => false,
            'batch_imported' => $imported,
            'total_imported' => $newOffset,
            'total_products' => $total,
            'progress_percentage' => round(($newOffset / $total) * 100, 2),
            'errors' => $errors,
            'next_batch_url' => url('/tiendanube/process-batch')
        ]);
    }

    public function getProducts()
    {
        $token = session('tiendanube_token');
        $userId = session('tiendanube_user_id');

        if (!$token) {
            return response()->json([
                'error' => 'No hay token. Primero debés autorizar la app.'
            ], 401);
        }

        try {
            // Traer productos de la API
            $response = Http::withHeaders([
                'Authentication' => 'bearer ' . $token,
                'User-Agent' => config('services.tiendanube.user_agent')
            ])->get("https://api.tiendanube.com/v1/{$userId}/products");

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Error al traer productos',
                    'details' => $response->json()
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al conectar con la API',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}