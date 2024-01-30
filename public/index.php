<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot.php';

$dbHost = \Up\Service\Configuration::getInstance()->option('DB_HOST');
$dbUser = \Up\Service\Configuration::getInstance()->option('DB_USER');
$dbPassword = \Up\Service\Configuration::getInstance()->option('DB_PASSWORD');
$dbName = \Up\Service\Configuration::getInstance()->option('DB_NAME');

$connection = \Up\Service\Database::getInstance($dbHost, $dbUser, $dbPassword, $dbName)->getDbConnection();

/*$result = mysqli_query(
	$connection,
	'SELECT * FROM up_item'
);

var_dump($result->fetch_all());*/

\Up\Service\Migration::migrate($connection);

echo 'done';

// $dbHost, $dbUser, $dbPassword, $dbName