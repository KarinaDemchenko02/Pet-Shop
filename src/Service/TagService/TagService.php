<?php

namespace Up\Service\TagService;


use Up\Dto\Tag\TagChangingDto;
use Up\Dto\TagDto;
use Up\Exceptions\Admin\Tag\TagNotChanged;
use Up\Exceptions\Tag\TagNotAdding;
use Up\Repository\Tag\TagRepositoryImpl;
use Up\Util\Database\Tables\TagTable;

class TagService
{
	public static function getAllTags(): array
	{
		$tags = TagRepositoryImpl::getAll();

		$tagsDto = [];
		foreach ($tags as $tag)
		{
			$tagsDto[] = new TagDto($tag);
		}

		return $tagsDto;
	}
	public static function getTagById(int $id): TagDto
	{
		$product = TagRepositoryImpl::getById($id);
		return new TagDto($product);
	}

	public static function deleteTag(int $id)
	{
		TagRepositoryImpl::delete($id);
	}

	/**
	 * @throws TagNotChanged
	 */
	public static function changeTag(TagChangingDto $dto): void
	{
		TagRepositoryImpl::change($dto);
	}

	public static function getColumn(): array
	{
		return TagTable::getColumnsName();
	}

	public static function getAllProductsForAdmin(int $page = 1): array
	{
		$tags = TagRepositoryImpl::getAllForAdmin($page);
		$tagsDto = [];
		foreach ($tags as $tag)
		{
			$tagsDto[] = new TagDto($tag);
		}

		return $tagsDto;
	}

	/**
	 * @throws TagNotAdding
	 */
	public static function addTag(string $title): int | string
	{
		return TagRepositoryImpl::add($title);
	}
}
