<?php

namespace Up\Repository\Tag;

use Up\Dto\Tag\TagChangingDto;
use Up\Entity\Tag;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Admin\Order\OrderNotDeleted;
use Up\Exceptions\Admin\Tag\TagNotChanged;
use Up\Util\Database\Query;

class TagRepositoryImpl implements TagRepository
{

	public static function getAll(): array
	{
		$query = Query::getInstance();
		$sql = "select * from up_tags;";

		$result = $query->getQueryResult($sql);

		$tags = [];

		while ($row = mysqli_fetch_assoc($result))
		{
			$tags[$row['id']] = new Tag($row['id'], $row['name']);
		}

		return $tags;
	}

	public static function getById(int $id): Tag
	{
		$query = Query::getInstance();
		$sql = "select * from up_tags where id = {$id};";

		$result = $query->getQueryResult($sql);

		$row = mysqli_fetch_assoc($result);

		return new Tag($row['id'], $row['name']);
	}

	public static function add(string $title): bool
	{
		$query = Query::getInstance();
		$sql = "INSERT INTO up_tags (name) VALUES ('{$title}');";

		$result = $query->getQueryResult($sql);

		return true;
	}

	public static function getColumn(): array
	{
		$query = Query::getInstance();
		$sql = "SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_NAME = 'up_tags'";
		$result = $query->getQueryResult($sql);
		$columns = [];
		while ($column = mysqli_fetch_column($result))
		{
			$columns[] = $column;
		}
		return $columns;
	}

	public static function delete(int $id)
	{
		/*$query = Query::getInstance();
		try
		{
			$query->begin();
			$deleteLinkOrderSQL = "DELETE FROM up_order_item WHERE order_id=$id";
			$query->getQueryResult($deleteLinkOrderSQL);
			$deleteOrderSQL = "DELETE FROM up_order WHERE id=$id";
			$query->getQueryResult($deleteOrderSQL);
			if (Query::affectedRows() === 0)
			{
				throw new OrderNotDeleted();
			}
			$query->commit();
		}
		catch (\Throwable)
		{
			$query->rollback();
			throw new OrderNotDeleted();
		}*/
	}

	/**
	 * @throws TagNotChanged
	 */
	public static function change(TagChangingDto $dto): void
	{
		$query = Query::getInstance();
		try
		{
			$query->begin();
			$changeOrderSQL = "
				UPDATE up_tags
				SET name='{$dto->title}'
				WHERE id={$dto->id}";
			$query->getQueryResult($changeOrderSQL);

			// $deleteItemLinkSQL = "DELETE FROM up_order_item WHERE item_id NOT IN ($itemIds)";
			// $query->getQueryResult($deleteItemLinkSQL);
			// foreach ($order->getProducts() as $item)
			// {
			// 	$addLinkToItemSQL = "INSERT IGNORE INTO up_order_item (order_id, item_id, quantities, price)
			// 						VALUES ({$order->id}, {$item->info->id}, {$item->getQuantity()}, {$item->info->price})";
			// 	$query->getQueryResult($addLinkToItemSQL);
			// }
			if (Query::affectedRows() === 0)
			{
				throw new TagNotChanged();
			}
			$query->commit();
		}
		catch (\Throwable|TagNotChanged)
		{
			$query->rollback();
			throw new TagNotChanged();
		}
	}
}
