<?php

namespace Up\Util\Database;

class Orm
{
	private static $instance;
	private \mysqli $db;
	private const encoding = 'utf8';

	private function __construct()
	{
		$this->db = new \mysqli(
			\Up\Util\Configuration::getInstance()->option('DB_HOST'),
			\Up\Util\Configuration::getInstance()->option('DB_USER'),
			\Up\Util\Configuration::getInstance()->option('DB_PASSWORD'),
			\Up\Util\Configuration::getInstance()->option('DB_NAME')
		);
		if ($this->db->connect_errno)
		{
			throw new \RuntimeException("Mysql connection error: " . $this->db->error);
		}

		$this->db->set_charset(self::encoding);
		if ($this->db->errno)
		{
			throw new \RuntimeException("Mysql encoding error: " . $this->db->error);
		}
	}

	private function __clone()
	{
	}

	public function __wakeup()
	{
	}

	public static function getInstance(): Orm
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function select($table, $columns = '*', $where = '', $orderBy = '', $limit = '', $offset='', $joins = [])
	{
		if (!is_string($columns))
			{
				$columns = implode(', ', $columns);
			}
		$query = "SELECT $columns FROM $table";

		foreach ($joins as $join => $joinData)
		{
			$joinType = $joinData['type'];
			$condition = $joinData['condition'];
			$query .= " $joinType JOIN $join ON $condition";
		}

		if (!empty($where))
		{
			$query .= " WHERE $where";
		}
		if (!empty($orderBy))
		{
			$query .= " ORDER BY $orderBy";
		}
		if (!empty($limit))
		{
			$query .= " LIMIT $limit";
		}
		if (!empty(!$offset))
		{
			$query .= " OFFSET $offset";
		}
		$stmt = $this->db->query($query);
		if ($error = $this->db->error)
		{
			throw new \RuntimeException($error);
		}

		return $stmt;
	}

	public function insert(string $table, array $data): int
	{
		$columns = implode(', ', array_keys($data));
		$values = implode(', ', array_map([$this, 'escapeString'], array_values($data)));
		$query = "INSERT INTO $table ($columns) VALUES ($values)";


		$result = $this->db->query($query);
		if ($error = $this->db->error)
		{
			throw new \RuntimeException($error);
		}

		return $result;
	}

	public function update(string $table, array $data, string $where): int
	{
		$columns = [];
		foreach ($data as $column => $value)
		{
			$value = $this->escapeString($value);
			$columns[] = "$column=$value";
		}
		$columns = implode(', ', $columns);
		$query = "UPDATE $table SET $columns WHERE $where";

		$result = $this->db->query($query);
		if ($error = $this->db->error)
		{
			throw new \RuntimeException($error);
		}

		return $result;
	}

	public function delete(string $table, string $where): int
	{
		$query = "DELETE FROM $table WHERE $where";
		$result = $this->db->query($query);

		if ($error = $this->db->error)
		{
			throw new \RuntimeException($error);
		}

		return $result;
	}

	public function execute($sql): void
	{
		$this->db->multi_query($sql);
		do
		{
			if ($error = $this->db->error)
			{
				throw new \RuntimeException($error);
			}
		}
		while ($this->db->next_result());
	}

	public function begin()
	{
		$this->db->begin_transaction();
	}

	public function commit()
	{
		$this->db->commit();
	}

	public function rollback()
	{
		$this->db->rollback();
	}

	private function escapeString($string)
	{
		if (!is_string($string))
		{
			return $string;
		}

		return "'{$this->db->real_escape_string($string)}'";
	}
}