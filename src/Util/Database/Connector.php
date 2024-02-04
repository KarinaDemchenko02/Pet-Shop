<?php

namespace Up\Util\Database;

use Up\Util\Singleton;

class Connector extends Singleton
{
	private \mysqli $connection;
	private const encoding = 'utf8';

	protected function initialize($params): void
	{
		$this->createConnection(...$params);
	}

	private function createConnection($dbHost, $dbUser, $dbPassword, $dbName): void
	{
		$this->connection = mysqli_init();

		$connected = mysqli_real_connect($this->connection, $dbHost, $dbUser, $dbPassword, $dbName);
		if (!$connected)
		{
			$error = mysqli_connect_errno() . ': ' . mysqli_connect_error();
			throw new \RuntimeException($error);
		}

		$encodingResult = mysqli_set_charset($this->connection, self::encoding);
		if (!$encodingResult)
		{
			throw new \RuntimeException(mysqli_error($this->connection));
		}
	}

	public function getDbConnection(): \mysqli
	{
		return $this->connection;
	}
}
