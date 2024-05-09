<?php

namespace Up\Util\Database;

use Up\Util\Configuration;

class Migration
{
	protected const  migrationPattern = "/\d{4}_(\d{2}_){4}/";

	public static function migrate(): void
	{
		$orm = Orm::getInstance();
		$lastFileTimestamp = 0;
		try
		{
			$migration = $orm->select('migration', 'NAME')->fetch_column();
			$lastFileTimestamp = self::getTimestampFromFileName($migration);

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
			self::doMigrations($orm, $lastFileTimestamp);
		}
	}

	private static function getTimestampFromFileName(string $file): int
	{
		preg_match(self::migrationPattern, $file, $date);
		$lastFileDate = substr($date[0], 0, -1);

		return (int)\DateTime::createFromFormat('Y_m_d_H_i', $lastFileDate)->getTimestamp();
	}

	private static function doMigrations(Orm $orm, $timeStamp = 0): void
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
			if(str_contains($migration, '_TABLE_NAME_'))
			{
				$dbName = Configuration::getInstance()->option('DB_NAME');
				$migration =  str_replace('_TABLE_NAME_', "'$dbName'", $migration);
			}
			$orm->executeMulti($migration);
			$lastFile = $file;
		}
		closedir($dh);
		if ($lastFile)
		{
			self::updateLastMigration($orm, $timeStamp === 0, $lastFile);
		}
	}

	private static function updateLastMigration(Orm $orm, bool $isFirstTime, $lastFile): void
	{
		if ($isFirstTime)
		{
			$orm->insert('migration', ['NAME' => $lastFile]);
		}
		else
		{
			$orm->update('migration', ['NAME' => $lastFile], 'ID=1');
		}
	}
}
