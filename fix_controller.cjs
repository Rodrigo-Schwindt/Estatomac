const fs = require('fs');
const file = 'c:/Users/SENSEI/Downloads/todo/todotex/app/Http/Controllers/Cliente/CarritoController.php';
let content = fs.readFileSync(file, 'utf8');

// 1. Update validation to add forma_entrega
content = content.replace(
    "'forma_pago' => 'required|in:contado,transferencia,cuenta_corriente',",
    "'forma_pago' => 'required|in:contado,transferencia,cuenta_corriente',\n            'forma_entrega' => 'nullable|in:entrega_1,entrega_2,entrega_3',"
);

// 2. After $config  = CarritoConfig::first(); compute costoEntrega
const oldConfig = "        $config  = CarritoConfig::first();\n        $cliente = Auth::guard('cliente')->user();";
const newConfig = "        $config  = CarritoConfig::first();\n        $cliente = Auth::guard('cliente')->user();\n\n        $costoEntrega = 0;\n        if ($config) {\n            if ($validated['forma_entrega'] === 'entrega_1') $costoEntrega = floatval($config->entrega_1_costo);\n            elseif ($validated['forma_entrega'] === 'entrega_2') $costoEntrega = floatval($config->entrega_2_costo);\n            elseif ($validated['forma_entrega'] === 'entrega_3') $costoEntrega = floatval($config->entrega_3_costo);\n        }";

if (!content.includes(oldConfig)) { console.log('CONFIG NOT FOUND'); process.exit(1); }
content = content.replace(oldConfig, newConfig);

// 3. Add costoEntrega to total
content = content.replace(
    "        $iva   = $subtotalFinal * ($porcentajeIva / 100);\n        $total = $subtotalFinal + $iva;",
    "        $iva   = $subtotalFinal * ($porcentajeIva / 100);\n        $total = $subtotalFinal + $iva + $costoEntrega;"
);

// 4. Add forma_entrega and costo_entrega to Pedido::create
content = content.replace(
    "                'forma_pago'             => $validated['forma_pago'],",
    "                'forma_pago'             => $validated['forma_pago'],\n                'forma_entrega'          => $validated['forma_entrega'] ?? null,\n                'costo_entrega'          => $costoEntrega,"
);

fs.writeFileSync(file, content);
console.log('OK');
