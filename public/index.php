<?php

use Up\Repository\Product\ProductRepositoryImpl;

require_once $_SERVER['DOCUMENT_ROOT'] . '/../' . '/boot.php';

$product = ProductRepositoryImpl::getById(1);
echo "<pre>We start" . PHP_EOL;

\Up\Repository\Session\ShoppingSessionImpl::add(2, new \Up\Entity\Cart([[$product, 2]]));


echo "We end" . PHP_EOL;
/*$application = new \Up\Application();
$application->run();*/
