<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Vendedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ClienteImportSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/files/06-05-26 Clientes.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        // Precargamos el mapa de vendedores por codigo_externo
        $vendedoresMap = Vendedor::whereNotNull('codigo_externo')
            ->pluck('id', 'codigo_externo')
            ->toArray();

        // Emails ya usados para detectar duplicados en el Excel
        $emailsUsados = Cliente::whereNotNull('email')->pluck('email')->flip()->toArray();

        $importados = 0;
        $actualizados = 0;
        $omitidos = 0;

        foreach ($sheet->getRowIterator(2) as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $cell->getValue();
            }

            // Columns (0-indexed):
            // 0:ClientesPK 1:Codigo 2:Cliente 3:NombreFantasia 4:Direccion 5:Telefono 6:Celular
            // 7:CodigoPostal 8:CiudadesPK 9:Ciudad 10:Provincia 11:EMail 12:Whatsapp
            // 13:CondicionesIVAPK 14:CondicionIVA 15:CUIT 16:ActivoSN
            // 17:CondicionesVentasPK 18:CondicionVenta 19:TiposOperacionesPK 20:TipoOperacion
            // 21:Descuento 22:TransportesPK 23:Transporte 24:VendedoresPK 25:Nombre(vendedor)
            // 26:RubrosClientesPK 27:RubroCliente 28:TiposDeListasPK 29:TipoDeLista
            // 30:CanalesPK 31:Canal 32:DescuentoCanal

            $codigo        = trim((string) ($cells[1] ?? ''));
            $nombre        = trim((string) ($cells[2] ?? ''));

            if (empty($codigo) || empty($nombre)) {
                $omitidos++;
                continue;
            }

            $nombreFantasia = $this->limpiarTexto($cells[3] ?? '');
            $domicilio      = $this->limpiarTexto($cells[4] ?? '');
            $telefono       = $this->limpiarTexto($cells[5] ?? '');
            $celular        = $this->limpiarTexto($cells[6] ?? '');
            $codigoPostal   = $this->limpiarTexto((string) ($cells[7] ?? ''));
            $localidad      = $this->limpiarTexto($cells[9] ?? '');
            $provincia      = $this->limpiarTexto($cells[10] ?? '');
            $email          = $this->limpiarEmail($cells[11] ?? '');
            $whatsapp       = $this->limpiarTexto($cells[12] ?? '');
            $condicionIva   = $this->limpiarTexto($cells[14] ?? '');
            $cuit           = $this->limpiarTexto((string) ($cells[15] ?? ''));
            $activo         = strtoupper(trim((string) ($cells[16] ?? 'N'))) === 'S';
            $condicionVenta = $this->limpiarTexto($cells[18] ?? '');
            $tipoOperacion  = $this->limpiarTexto($cells[20] ?? '');
            $descuento      = is_numeric($cells[21] ?? null) ? (float) $cells[21] : 0;
            $transporte     = $this->limpiarTexto($cells[23] ?? '');
            $vendedoresPk   = (int) ($cells[24] ?? 0);
            $rubroCliente   = $this->limpiarTexto($cells[27] ?? '');
            $tipoDeLista    = $this->limpiarTexto($cells[29] ?? '');
            $canal          = $this->limpiarTexto($cells[31] ?? '');
            $descuentoCanal = is_numeric($cells[32] ?? null) ? (float) $cells[32] : 0;

            // Resolver vendedor_id
            $vendedorId = $vendedoresMap[$vendedoresPk] ?? null;

            // Email: si ya fue usado en esta sesión o en la DB, guardamos null
            if ($email !== null) {
                if (isset($emailsUsados[$email])) {
                    $email = null;
                } else {
                    $emailsUsados[$email] = true;
                }
            }

            $existente = Cliente::where('codigo', $codigo)->first();

            if ($existente) {
                // Actualizar datos pero no tocar usuario ni password
                $existente->update([
                    'nombre'          => $nombre,
                    'nombre_fantasia' => $nombreFantasia,
                    'email'           => $email ?? $existente->email,
                    'cuit'            => $cuit,
                    'condicion_iva'   => $condicionIva,
                    'telefono'        => $telefono,
                    'celular'         => $celular,
                    'whatsapp'        => $whatsapp,
                    'domicilio'       => $domicilio,
                    'localidad'       => $localidad,
                    'codigo_postal'   => $codigoPostal,
                    'provincia'       => $provincia,
                    'condicion_venta' => $condicionVenta,
                    'tipo_operacion'  => $tipoOperacion,
                    'descuento'       => $descuento,
                    'transporte'      => $transporte,
                    'vendedor_id'     => $vendedorId,
                    'rubro_cliente'   => $rubroCliente,
                    'tipo_lista'      => $tipoDeLista,
                    'canal'           => $canal,
                    'descuento_canal' => $descuentoCanal,
                    'activo'          => $activo,
                ]);
                $actualizados++;
            } else {
                Cliente::create([
                    'codigo'          => $codigo,
                    'usuario'         => $codigo,
                    'password'        => Hash::make('todotex2025'),
                    'nombre'          => $nombre,
                    'nombre_fantasia' => $nombreFantasia,
                    'email'           => $email,
                    'cuit'            => $cuit,
                    'condicion_iva'   => $condicionIva,
                    'telefono'        => $telefono,
                    'celular'         => $celular,
                    'whatsapp'        => $whatsapp,
                    'domicilio'       => $domicilio,
                    'localidad'       => $localidad,
                    'codigo_postal'   => $codigoPostal,
                    'provincia'       => $provincia,
                    'condicion_venta' => $condicionVenta,
                    'tipo_operacion'  => $tipoOperacion,
                    'descuento'       => $descuento,
                    'transporte'      => $transporte,
                    'vendedor_id'     => $vendedorId,
                    'rubro_cliente'   => $rubroCliente,
                    'tipo_lista'      => $tipoDeLista,
                    'canal'           => $canal,
                    'descuento_canal' => $descuentoCanal,
                    'activo'          => $activo,
                ]);
                $importados++;
            }
        }

        $this->command->info("Clientes: {$importados} creados, {$actualizados} actualizados, {$omitidos} omitidos.");
    }

    private function limpiarTexto(?string $valor): ?string
    {
        if ($valor === null) return null;
        $valor = trim($valor);
        return $valor === '' ? null : $valor;
    }

    private function limpiarEmail(?string $valor): ?string
    {
        if ($valor === null) return null;
        $valor = trim($valor);
        if ($valor === '' || !str_contains($valor, '@')) {
            return null;
        }
        return strtolower($valor);
    }
}
