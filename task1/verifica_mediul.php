<?php
header('Content-Type: text/plain; charset=UTF-8');
$versiuneCorecta = PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 3;
$arhitecturaCorecta = PHP_INT_SIZE === 8;
echo 'PHP: ' . PHP_VERSION . PHP_EOL;
echo 'Arhitectură: ' . (PHP_INT_SIZE * 8) . ' biți' . PHP_EOL;
echo 'Interfață: ' . PHP_SAPI . PHP_EOL;
echo 'Cerință PHP 8.3 x64: ' . ($versiuneCorecta && $arhitecturaCorecta ? 'ÎNDEPLINITĂ' : 'NEÎNDEPLINITĂ') . PHP_EOL;
