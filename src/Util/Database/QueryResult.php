<?php

namespace Up\Util\Database;

class QueryResult
{
	public static function getQueryResult(string $sql): \mysqli_result|bool
	{
		$connection = Connector::getInstance()->getDbConnection();

		$result = mysqli_query($connection, $sql);

		if (!$result)
		{
			throw new \RuntimeException(mysqli_error($connection));
		}

		return $result;
	}
}
