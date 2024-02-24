<?php

namespace Up\Util\Database;

class Query
{

	private static ?Query $instance = null;
	private static \mysqli $connection;

	private function __construct()
	{
		self::$connection = Connector::getInstance()->getDbConnection();
	}

	public function getQueryResult(string $sql): \mysqli_result|bool
	{
		$result = mysqli_query(self::$connection, $sql);

		if (!$result)
		{
			throw new \RuntimeException(mysqli_error(self::$connection));
		}

		return $result;
	}

	public function execute($sql): void
	{
		mysqli_multi_query(self::$connection, $sql);
		do
		{
			if ($error = mysqli_error(self::$connection))
			{
				throw new \RuntimeException($error);
			}
		}
		while (mysqli_next_result(self::$connection));
	}

	public function begin(): void
	{
		mysqli_begin_transaction(self::$connection);
	}

	public function commit(): void
	{
		mysqli_commit(self::$connection);
	}

	public function rollback(): void
	{
		mysqli_rollback(self::$connection);
	}

	public function last(): int|string
	{
		return mysqli_insert_id(self::$connection);
	}

	public function escape(string $string): string
	{
		return mysqli_real_escape_string(self::$connection, $string);
	}

	public function affectedRows(): int|string
	{
		return mysqli_affected_rows(self::$connection);
	}

	public function getError()
	{
		return mysqli_error(self::$connection);
	}

	public static function getInstance(): Query
	{
		if (static::$instance)
		{
			return static::$instance;
		}

		static::$instance = new self();

		return static::$instance;
	}
}
