<?php

use Up\Repository\Product\ProductRepositoryImpl;
use Up\Util\Database\Orm;


require_once $_SERVER['DOCUMENT_ROOT'] . '/../' . '/boot.php';

$application = new \Up\Application();
$application->run();
