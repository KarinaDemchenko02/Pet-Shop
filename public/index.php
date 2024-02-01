<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot.php';

$connection = \Up\Service\Database::getInstance(
	\Up\Service\Configuration::getInstance()->option('DB_HOST'),
	\Up\Service\Configuration::getInstance()->option('DB_USER'),
	\Up\Service\Configuration::getInstance()->option('DB_PASSWORD'),
	\Up\Service\Configuration::getInstance()->option('DB_NAME')
)->getDbConnection();

\Up\Service\Migration::migrate($connection);