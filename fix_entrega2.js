const fs = require('fs');
const bladeFile = 'c:/Users/SENSEI/Downloads/todo/todotex/resources/views/livewire/zona/carrito-zona.blade.php';
const newSectionFile = 'c:/Users/SENSEI/Downloads/todo/todotex/fix_entrega_new.txt';

let content = fs.readFileSync(bladeFile, 'utf8');
const newSection = fs.readFileSync(newSectionFile, 'utf8');

const startMarker = '                    <div class="border border-gray-200 mb-[26px]">\r\n                        <div class="bg-white text-black px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[4px]">\r\n                            <h3 class="font-semibold border-b border-gray-200 text-[20px] pt-[18px] w-full pb-[15px] sm:text-[18px] font-semibold">Entrega</h3>';
const startMarkerLF = '                    <div class="border border-gray-200 mb-[26px]">\n                        <div class="bg-white text-black px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[4px]">\n                            <h3 class="font-semibold border-b border-gray-200 text-[20px] pt-[18px] w-full pb-[15px] sm:text-[18px] font-semibold">Entrega</h3>';

const nextSectionMarker = 'Formas de pago</h3>';

let startIdx = content.indexOf(startMarker);
if (startIdx === -1) startIdx = content.indexOf(startMarkerLF);
if (startIdx === -1) { console.log('START NOT FOUND'); process.exit(1); }

const nextIdx = content.indexOf(nextSectionMarker);
if (nextIdx === -1) { console.log('NEXT NOT FOUND'); process.exit(1); }

// Find the closing </div> before the next section
// Go backwards from nextIdx to find the start of next section div
let nextSectionStart = content.lastIndexOf('<div class="border border-gray-200 mb-[26px]">', nextIdx);
console.log('startIdx:', startIdx, 'nextSectionStart:', nextSectionStart);

const oldSection = content.substring(startIdx, nextSectionStart);
console.log('Old section length:', oldSection.length);
console.log('Old section start:', JSON.stringify(oldSection.substring(0, 80)));
console.log('Old section end:', JSON.stringify(oldSection.substring(oldSection.length - 80)));

content = content.substring(0, startIdx) + newSection + '\n                    ' + content.substring(nextSectionStart);
fs.writeFileSync(bladeFile, content);
console.log('OK');
