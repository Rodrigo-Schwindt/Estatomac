const fs = require('fs');
const file = 'c:/Users/SENSEI/Downloads/todo/todotex/app/Http/Controllers/Cliente/CarritoController.php';
let content = fs.readFileSync(file, 'utf8');

// 1. Update validation to add forma_entrega
const oldValidation = "'forma_pago' => 'required|in:contado,transferencia,cuenta_corriente',";
const newValidation = "'forma_pago' => 'required|in:contado,transferencia,cuenta_corriente',\r\n            'forma_entrega' => 'nullable|in:entrega_1,entrega_2,entrega_3',";
content = content.replace(oldValidation, newValidation);

// 2. Insert costoEntrega computation after $cliente line
const configIdx = content.indexOf('CarritoConfig::first();');
const clienteLineEnd = content.indexOf('->user();', configIdx) + '->user();'.length;

const costoCode = "\r\n\r\n        $costoEntrega = 0;\r\n        if ($config) {\r\n            $formaEntrega = $validated['forma_entrega'] ?? 'entrega_1';\r\n            if ($formaEntrega === 'entrega_1') $costoEntrega = floatval($config->entrega_1_costo);\r\n            elseif ($formaEntrega === 'entrega_2') $costoEntrega = floatval($config->entrega_2_costo);\r\n            elseif ($formaEntrega === 'entrega_3') $costoEntrega = floatval($config->entrega_3_costo);\r\n        }";

content = content.slice(0, clienteLineEnd) + costoCode + content.slice(clienteLineEnd);

// 3. Add costoEntrega to total
content = content.replace(
    '$total = $subtotalFinal + $iva;',
    '$total = $subtotalFinal + $iva + $costoEntrega;'
);

// 4. Add forma_entrega and costo_entrega to Pedido::create
content = content.replace(
    "'forma_pago'             => $validated['forma_pago'],",
    "'forma_pago'             => $validated['forma_pago'],\r\n                'forma_entrega'          => $validated['forma_entrega'] ?? null,\r\n                'costo_entrega'          => $costoEntrega,"
);

fs.writeFileSync(file, content);
console.log('OK');
