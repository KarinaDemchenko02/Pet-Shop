<?php

namespace Up\Util\Database;

class QueryResult
{
	static function getQueryResult(string $sql): \mysqli_result|bool
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