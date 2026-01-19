<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;

class ProductoImportController extends Controller
{
    public function showImportForm()
    {
        return view('livewire.productos.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $import = new ProductosImport();
            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();

            return redirect()->route('productos.index')->with('success', 
                "Importación completada. Actualizados: {$stats['updated']}, No encontrados: {$stats['not_found']}, Errores: {$stats['errors']}"
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ];

        $columns = ['code', 'title', 'precio', 'descuento', 'visible', 'nuevo', 'oferta', 'importados'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, $columns);
            
            // Fila de ejemplo
            fputcsv($file, [
                'PROD001',           // code
                'Producto Ejemplo',  // title
                '100.50',           // precio
                '10.00',            // descuento
                '1',                // visible (1=si, 0=no)
                'nuevo',            // nuevo (nuevo/recambio)
                '0',                // oferta (1=si, 0=no)
                '1'                 // importados (1=si, 0=no)
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}