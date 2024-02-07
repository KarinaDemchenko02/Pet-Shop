<?php

namespace Up\Service;

class Migration
{
	protected const  migrationPattern = "/\d{4}_(\d{2}_){4}/";

	public static function migrate($connection): void
	{
		$lastFileTimestamp = 0;
		try
		{
			$result = mysqli_query(
				$connection,
				'SELECT NAME FROM migration'
			);
			if (!$result)
			{
				throw new \RuntimeException(mysqli_error($connection));
			}
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
			self::doMigrations($connection, $lastFileTimestamp);
		}
	}

	private static function getTimestampFromFileName(string $file): int
	{
		preg_match(self::migrationPattern, $file, $date);
		$lastFileDate = substr($date[0], 0, -1);

		return (int)\DateTime::createFromFormat('Y_m_d_H_i', $lastFileDate)->getTimestamp();
	}

	private static function doMigrations($connection, $timeStamp = 0): void
	{
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
			mysqli_multi_query($connection, $migration);
			do
			{
				if ($error = mysqli_error($connection))
				{
					throw new \RuntimeException($error);
				}
			}
			while (mysqli_next_result($connection));
			$lastFile = $file;
		}

		closedir($dh);
		if ($lastFile)
		{
			self::updateLastMigration($connection, $timeStamp === 0, $lastFile);
		}
	}

	private static function updateLastMigration($connection, bool $isFirstTime, $lastFile): void
	{
		if ($isFirstTime)
		{
			$result = mysqli_query(
				$connection,
				"INSERT INTO migration (NAME) VALUES ('$lastFile')"
			);
		}
		else
		{
			$result = mysqli_query(
				$connection,
				"UPDATE migration SET NAME='$lastFile' WHERE ID =1;"
			);
		}
		if (!$result)
		{
			throw new \RuntimeException(mysqli_error($connection));
		}
	}
}