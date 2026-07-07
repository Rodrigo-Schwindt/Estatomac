<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkImportLog;
use App\Services\Imports\BajaRotacionImporter;
use App\Services\Imports\BulkImportRunner;
use App\Services\Imports\ClientesImporter;
use App\Services\Imports\ProductosImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BulkImportController extends Controller
{
    public function index()
    {
        $logs = BulkImportLog::query()->orderByDesc('id')->limit(30)->get();
        return view('admin.imports.index', compact('logs'));
    }

    public function importClientes(Request $request, BulkImportRunner $runner)
    {
        $request->validate(['archivo' => 'required|file|mimes:xls,xlsx,csv|max:20480']);

        $log = $runner->run('clientes_canales', $request->file('archivo'), new ClientesImporter());

        return $this->redirectWith($log);
    }

    public function importProductos(Request $request, BulkImportRunner $runner)
    {
        $request->validate([
            'archivo'           => 'required|file|mimes:xls,xlsx,csv|max:20480',
            'lista_numero'      => 'nullable|integer',
            'lista_titulo'      => 'nullable|string|max:255',
            'lista_fecha_desde' => 'nullable|date',
            'lista_vigente'     => 'nullable|boolean',
            'lista_archivo'     => 'nullable|file|mimes:pdf,xls,xlsx|max:10240',
        ]);

        $importer                  = new ProductosImporter();
        $importer->listaNumero     = $request->filled('lista_numero') ? (int) $request->lista_numero : null;
        $importer->listaTitulo     = $request->lista_titulo;
        $importer->listaFechaDesde = $request->lista_fecha_desde;
        $importer->listaVigente    = $request->boolean('lista_vigente');
        $importer->listaArchivo    = $request->file('lista_archivo');

        $log = $runner->run('productos_listaprecio', $request->file('archivo'), $importer);

        return $this->redirectWith($log);
    }

    public function importBajaRotacion(Request $request)
    {
        $request->validate(['archivo' => 'required|file|mimes:xls,xlsx|max:20480']);

        $archivo = $request->file('archivo');
        $log = BulkImportLog::create([
            'proceso' => 'baja_rotacion',
            'archivo' => $archivo->getClientOriginalName(),
            'estado'  => BulkImportLog::ESTADO_OK,
            'user_id' => Auth::id(),
        ]);

        try {
            $procesadas = DB::transaction(fn () => (new BajaRotacionImporter())->fromFile($archivo));

            $log->update([
                'estado'           => BulkImportLog::ESTADO_OK,
                'filas_procesadas' => $procesadas,
                'mensaje'          => "Carga completada: {$procesadas} fila(s).",
            ]);
        } catch (Throwable $e) {
            Log::error('BulkImport [baja_rotacion] error', ['exception' => $e]);
            $log->update([
                'estado'  => BulkImportLog::ESTADO_ERROR,
                'mensaje' => 'Error: ' . $e->getMessage() . ' (rollback aplicado, los datos originales quedan operativos)',
            ]);
        }

        return $this->redirectWith($log);
    }

    private function redirectWith(BulkImportLog $log)
    {
        $key = $log->estado === BulkImportLog::ESTADO_OK ? 'success' : 'error';
        return redirect()->route('admin.imports.index')->with($key, $log->mensaje);
    }

    /**
     * Dispara el comando imports:procesar-zip-erp manualmente desde el botón
     * del admin de productos. Procesa el ZIP más reciente del FTP `imports-erp/in/`.
     */
    public function procesarErpZip(Request $request)
    {
        // El import puede tardar varios minutos en producción (~1.300 productos +
        // 7.600 importes + sync + fallback + mapeos). Sin esto, PHP-FPM corta a
        // los 30s y devuelve 500 sin loguear nada.
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $exitCode = Artisan::call('imports:procesar-zip-erp');
            $output = Artisan::output();

            // Buscamos el último log del proceso erp_zip para devolver feedback útil
            $log = BulkImportLog::where('proceso', 'erp_zip')->latest('id')->first();

            if ($exitCode === 0 && $log && $log->estado === BulkImportLog::ESTADO_OK) {
                return back()->with('success', $log->mensaje ?: 'Import del ERP procesado correctamente. ' . trim($output));
            }

            if ($exitCode === 0) {
                return back()->with('success', 'No había ZIP nuevo para procesar. ' . trim($output));
            }

            return back()->with('error', $log?->mensaje ?: 'Error procesando el ZIP. Revisá los logs.');
        } catch (Throwable $e) {
            Log::error('Procesar ZIP ERP manual: error', ['exception' => $e]);
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }
}
