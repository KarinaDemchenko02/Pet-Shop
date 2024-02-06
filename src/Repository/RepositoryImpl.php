<?php

namespace Up\Repository;

class RepositoryImpl
{
	public static function getResultSQLQuery(string $sql): \mysqli_result|bool
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new \Exception(mysqli_error($connection));
		}
		return $result;
	}
}