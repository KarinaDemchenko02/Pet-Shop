<?php

namespace Up\Service;

class Migration
{
	protected const  migrationPattern = "/\d{4}_(\d{2}_){4}/";

	public static function migrate($connection): void
	{
		try
		{
			$result = mysqli_query(
				$connection,
				'SELECT NAME FROM migration'
			);
			$fileName = $result->fetch_column();
			$dateLastFile = self::getTimestampFromFileName($fileName);
			self::doMigrations($connection, $dateLastFile);

		}
		catch (\mysqli_sql_exception $e)
		{
			if ($e->getSqlState() !== "42S02")
			{
				throw $e;
			}
			self::doMigrations($connection);
		}
	}

	private static function getTimestampFromFileName(string $file): int
	{
		preg_match(self::migrationPattern, $file, $date);
		$lastFileDate = substr($date[0], 0, -1);
		$parts = explode('_', $lastFileDate);
		$dateString = $parts[0] . '-' . $parts[1] . '-' . $parts[2] . ' ' . $parts[3] . ':' . $parts[4];

		return (int)\DateTime::createFromFormat('Y-m-d H:i', $dateString)->getTimestamp();
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
			$queries = explode(';', $migration);
			foreach ($queries as $query)
			{
				/*echo "Текущий запрос: $query <br>";*/
				$query = trim($query);
				if (!empty($query))
				{
					$result = mysqli_query($connection, $query);
					if (!$result)
					{
						throw new \RuntimeException(mysqli_error($connection));
					}
				}
			}
			$lastFile = $file;
		}
		if ($timeStamp === 0)
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

		closedir($dh);
	}
}


/*/[0-9]{4}_([0-9]{2}_){4}/*/