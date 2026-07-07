const fs = require('fs');
const file = 'c:/Users/SENSEI/Downloads/todo/todotex/resources/views/livewire/zona/carrito-zona.blade.php';
let content = fs.readFileSync(file, 'utf8');

// 1. Add hidden input for forma_entrega after @csrf in the form
const csrfLine = '            @csrf\n';
const csrfLineWithHidden = '            @csrf\n            <input type="hidden" name="forma_entrega" :value="$wire.formEntrega">\n';
if (!content.includes(csrfLine)) { console.log('CSRF NOT FOUND'); process.exit(1); }
content = content.replace(csrfLine, csrfLineWithHidden);

// 2. Add entrega cost line in "Tu pedido" section - after the IVA div
const ivaSection = '                        <div class="border-t border-gray-200 pt-4">\n                            <div class="flex justify-between items-center gap-2">\n                                <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">IVA';
const ivaSectionReplacement = '                        @if($costoEntrega > 0)\n                        <div class="flex justify-between items-center gap-2">\n                            <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Entrega</span>\n                            <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($costoEntrega, 2, \',\', \'.\') }}</span>\n                        </div>\n                        @endif\n\n                        <div class="border-t border-gray-200 pt-4">\n                            <div class="flex justify-between items-center gap-2">\n                                <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">IVA';

if (!content.includes(ivaSection)) { console.log('IVA SECTION NOT FOUND'); process.exit(1); }
content = content.replace(ivaSection, ivaSectionReplacement);

fs.writeFileSync(file, content);
console.log('OK');
