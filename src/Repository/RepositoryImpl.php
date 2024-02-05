<?php

namespace Up\Repository;

class RepositoryImpl
{
	public static function getResultSQLQuery(string $sql): \mysqli_result|bool
	{
		$connection = \Up\Util\Database\Connector::getInstance(
			\Up\Util\Configuration::getInstance()->option('DB_HOST'),
			\Up\Util\Configuration::getInstance()->option('DB_USER'),
			\Up\Util\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Configuration::getInstance()->option('DB_NAME')
		)->getDbConnection();

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new \Exception(mysqli_error($connection));
		}
		return $result;
	}
}