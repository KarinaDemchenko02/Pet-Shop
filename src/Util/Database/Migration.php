<?php

namespace Up\Util\Database;

class Migration
{
	protected const  migrationPattern = "/\d{4}_(\d{2}_){4}/";

	public static function migrate(): void
	{
		$lastFileTimestamp = 0;
		$queryResult = QueryResult::getInstance();
		try
		{
			$result = $queryResult->getQueryResult('SELECT NAME FROM migration');
			$lastFileTimestamp = self::getTimestampFromFileName($result->fetch_column());
		}
		catch (\mysqli_sql_exception $e)
		{
			if ($e->getSqlState() !== "42S02")
			{
				throw $e;
			}
		}
		finally
		{
			self::doMigrations($lastFileTimestamp);
		}
	}

	private static function getTimestampFromFileName(string $file): int
	{
		preg_match(self::migrationPattern, $file, $date);
		$lastFileDate = substr($date[0], 0, -1);

		return (int)\DateTime::createFromFormat('Y_m_d_H_i', $lastFileDate)->getTimestamp();
	}

	private static function doMigrations($timeStamp = 0): void
	{
		$queryResult = QueryResult::getInstance();
		$dir = ROOT . '/Migration';
		$lastFile = null;
		if (!is_dir($dir))
		{
			throw new \RuntimeException("migration dir is not exists");
		}
		$dh = opendir($dir);
		while (($file = readdir($dh)) !== false)
		{
			if (!preg_match(self::migrationPattern, $file))
			{
				continue;
			}
			if (self::getTimestampFromFileName($file) <= $timeStamp)
			{
				continue;
			}
			$migration = file_get_contents($dir . '/' . $file);
			$queryResult->execute($migration);
			$lastFile = $file;
		}
		closedir($dh);
		if ($lastFile)
		{
			self::updateLastMigration($timeStamp === 0, $lastFile);
		}
	}

	private static function updateLastMigration(bool $isFirstTime, $lastFile): void
	{
		$queryResult = QueryResult::getInstance();
		$sql = $isFirstTime ? "INSERT INTO migration (NAME) VALUES ('$lastFile')"
			: "UPDATE migration SET NAME='$lastFile' WHERE ID =1;";
		$queryResult->getQueryResult($sql);
	}
}
