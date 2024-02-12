<?php

use Up\Repository\Product\ProductRepositoryImpl;



require_once $_SERVER['DOCUMENT_ROOT'] . '/../' . '/boot.php';

$application = new \Up\Application();
$application->run();
