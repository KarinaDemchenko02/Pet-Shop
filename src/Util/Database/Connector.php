<?php

namespace Up\Util\Database;

class Connector
{
	private \mysqli $connection;
	private const encoding = 'utf8';
	private static ?Connector $instance = null;

	private function __construct()
	{
		$configuration = \Up\Util\Configuration::getInstance();
		$this->createConnection(
			$configuration->option('DB_HOST'),
			$configuration->option('DB_USER'),
			$configuration->option('DB_PASSWORD'),
			$configuration->option('DB_NAME')
		);
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

	public static function getInstance(): Connector
	{
		if (static::$instance)
		{
			return static::$instance;
		}

		static::$instance = new self();

		return static::$instance;
	}
}
