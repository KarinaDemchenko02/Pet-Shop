<?php

use Up\Repository\Product\ProductRepositoryImpl;
use Up\Util\Database\Orm;


require_once $_SERVER['DOCUMENT_ROOT'] . '/../' . '/boot.php';
echo '<pre>';
/*$columns = \Up\Util\Database\Tables\OrderTable::getColumnJoin(['id', 'delivery_address', 'name'], ['user', 'role' => ['title']], columnAliases: ['OrderTable.id' => 'order_id', 'RoleTable.title' => 'role_title']);*/
$columns = \Up\Util\Database\Tables\OrderTable::getColumnJoin(['*'], ['product', 'tag' => ['id'], 'image' => ['path']], columnAliases: ['TagTable.id' => 'tag_id', 'ProductTable.id' => 'product_id']);
var_dump($columns);
$result = Orm::getInstance()->select('up_order', $columns['columns'], joins: $columns['joins']);
while ($row = $result->fetch_assoc())
{
	var_dump($row);
}

die;
$application = new \Up\Application();
$application->run();
