<?php
$file = 'database/seeders/NewcostSeeder.php';
$content = file_get_contents($file);
$content = str_replace(
    "                'descripcion'       => \$descripcion,",
    "                'titulo'            => mb_substr(\$descripcion, 0, 255),\n                'descripcion'       => \$descripcion,",
    $content
);
file_put_contents($file, $content);
echo "OK\n";
echo "Check: " . (strpos($content, "'titulo'") !== false ? "titulo found" : "titulo NOT found") . "\n";
