<?php

use Up\Repository\Product\ProductRepositoryImpl;

require_once $_SERVER['DOCUMENT_ROOT'] . '/../' . '/boot.php';
echo '<pre>';

$result = \Up\Util\Database\Tables\ProductTable::getAll();
while ($row = $result->fetch_assoc())
{
	echo '<pre>';
	var_dump($row);
}
die;
$application = new \Up\Application();
$application->run();
