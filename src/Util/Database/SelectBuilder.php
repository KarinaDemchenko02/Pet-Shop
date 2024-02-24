<?php

namespace Up\Util\Database;

class SelectBuilder
{
	private $table;
	private $columns = '*';
	private $where = '';
	private $orderBy = '';
	private $limit = '';
	private $offset = '';
	private $joins = [];

	public function setTable($table)
	{
		$this->table = $table;

		return $this;
	}

	public function setColumns($columns)
	{
		$this->columns = $columns;

		return $this;
	}

	public function setWhere($where)
	{
		$this->where = $where;

		return $this;
	}

	public function setOrderBy($orderBy)
	{
		$this->orderBy = $orderBy;

		return $this;
	}

	public function setLimit($limit)
	{
		$this->limit = $limit;

		return $this;
	}

	public function setOffset($offset)
	{
		$this->offset = $offset;

		return $this;
	}

	public function addJoin($table, $type, $condition)
	{
		$this->joins[$table] = ['type' => $type, 'condition' => $condition];

		return $this;
	}

	public function execute()
	{
		return Orm::getInstance()->select(
			$this->table,
			$this->columns,
			$this->where,
			$this->orderBy,
			$this->limit,
			$this->offset,
			$this->joins
		);
	}
}