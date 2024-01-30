<?php

namespace Up\Service;

class Database extends BaseSingletonService
{
	private $connection;

	public function __construct()
	{
		$this->createConnection(
			Configuration::getInstance()->option('DB_HOST'),
			Configuration::getInstance()->option('DB_USER'),
			Configuration::getInstance()->option('DB_PASSWORD'),
			Configuration::getInstance()->option('DB_NAME')
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

		$encodingResult = mysqli_set_charset($this->connection, 'utf8');
		if (!$encodingResult)
		{
			throw new \RuntimeException(mysqli_error($this->connection));
		}
	}

	public function getDbConnection()
	{
		return $this->connection;
	}
}