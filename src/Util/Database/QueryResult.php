<?php

namespace Up\Util\Database;

class QueryResult
{

	private static ?QueryResult $instance = null;
	private \mysqli $connection;

	private function __construct()
	{
		$this->connection = Connector::getInstance()->getDbConnection();
	}

	public function getQueryResult(string $sql): \mysqli_result|bool
	{
		$result = mysqli_query($this->connection, $sql);

		if (!$result)
		{
			throw new \RuntimeException(mysqli_error($this->connection));
		}

		return $result;
	}

	public function execute($sql): void
	{;
		mysqli_multi_query($this->connection, $sql);
		do
		{
			if ($error = mysqli_error($this->connection))
			{
				throw new \RuntimeException($error);
			}
		}
		while (mysqli_next_result($this->connection));
	}

	public static function getInstance(): QueryResult
	{
		if (static::$instance)
		{
			return static::$instance;
		}

		static::$instance = new self();

		return static::$instance;
	}
}

