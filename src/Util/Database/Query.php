<?php

namespace Up\Util\Database;

class Query
{

	private static ?Query $instance = null;
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
	{
		;
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

	public function begin(): void
	{
		mysqli_begin_transaction($this->connection);
	}

	public function commit(): void
	{
		mysqli_commit($this->connection);
	}

	public function rollback(): void
	{
		mysqli_rollback($this->connection);
	}

	public function last(): int|string
	{
		return mysqli_insert_id($this->connection);
	}

	public function escape(string $string): string
	{
		return mysqli_real_escape_string($this->connection, $string);
	}

	public function affectedRows(): int|string
	{
		return mysqli_affected_rows($this->connection);
	}

	public function getError()
	{
		return mysqli_error($this->connection);
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

