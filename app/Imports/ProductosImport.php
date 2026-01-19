<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private $updated = 0;
    private $not_found = 0;
    private $errors = 0;

    public function model(array $row)
    {
        // Buscar producto por código
        $producto = Producto::where('code', $row['code'])->first();

        if (!$producto) {
            $this->not_found++;
            return null;
        }

        try {
            // Actualizar campos
            if (isset($row['title']) && !empty($row['title'])) {
                $producto->title = $row['title'];
            }

            if (isset($row['precio']) && !empty($row['precio'])) {
                $producto->precio = $row['precio'];
            }

            if (isset($row['descuento'])) {
                $producto->descuento = $row['descuento'] ?? 0;
            }

            if (isset($row['visible'])) {
                $producto->visible = filter_var($row['visible'], FILTER_VALIDATE_BOOLEAN);
            }

            if (isset($row['nuevo']) && in_array($row['nuevo'], ['nuevo', 'recambio'])) {
                $producto->nuevo = $row['nuevo'];
            }

            if (isset($row['oferta'])) {
                $producto->oferta = filter_var($row['oferta'], FILTER_VALIDATE_BOOLEAN);
            }

            if (isset($row['importados'])) {
                $producto->importados = filter_var($row['importados'], FILTER_VALIDATE_BOOLEAN);
            }

            $producto->save();
            $this->updated++;

            return $producto;
        } catch (\Exception $e) {
            $this->errors++;
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'title' => 'nullable|string|max:255',
            'precio' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'visible' => 'nullable|boolean',
            'nuevo' => 'nullable|in:nuevo,recambio',
            'oferta' => 'nullable|boolean',
            'importados' => 'nullable|boolean',
        ];
    }

    public function getStats()
    {
        return [
            'updated' => $this->updated,
            'not_found' => $this->not_found,
            'errors' => $this->errors,
        ];
    }
}