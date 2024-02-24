<?php

namespace Up\Service\ProductService;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Dto\ProductDto;
use Up\Dto\ProductDtoAdmin;
use Up\Exceptions\Admin\ProductNotChanged;
use Up\Exceptions\Admin\ProductNotDisabled;
use Up\Exceptions\Admin\ProductNotRestored;
use Up\Repository\Product\ProductRepositoryImpl;


class ProductService
{
	public static function getAllProducts(int $page): array
	{

		$products = ProductRepositoryImpl::getAll($page);

		$productsDto = [];
		foreach ($products as $product)
		{
			$productsDto[] = new ProductDto($product);
		}

		return $productsDto;
	}
	public static function getProductById(int $id): ProductDto
	{
		$product = ProductRepositoryImpl::getById($id);
		return new ProductDto($product);
	}

	public static function getProductsByTag(int $tagId, int $page = 1): array
	{
		$products = ProductRepositoryImpl::getByTag($tagId, $page);

		$productsDto = [];
		foreach ($products as $product)
		{
			$productsDto[] = new ProductDto($product);
		}

		return $productsDto;
	}

	public static function getProductsBySpecialOffer(int $specialOfferId, int $page):array
	{
		$products = ProductRepositoryImpl::getProductsBySpecialOffer($specialOfferId, $page);

		$productsDto = [];
		foreach ($products as $product)
		{
			$productsDto[] = new ProductDto($product);
		}

		return $productsDto;
	}

	public static function getProductByTitle(string $title, int $page): array
	{
		$title = strtolower(trim($title));
		$products = ProductRepositoryImpl::getByTitle($title, $page);

		$productsDto = [];
		foreach ($products as $product)
		{
			$productsDto[] = new ProductDto($product);
		}

		return $productsDto;
	}

	public static function getAllProductsForAdmin(int $page = 1): array
	{
		$products = ProductRepositoryImpl::getAllForAdmin($page);
		$productsDto = [];
		foreach ($products as $product)
		{
			$productsDto[] = new ProductDtoAdmin($product);
		}
		/*echo "<pre>";
		var_dump($productsDto); die;*/
		return $productsDto;
	}

	/**
	 * @throws ProductNotChanged
	 */
	public static function changeProduct(ProductChangeDto $productChangeDto): void
	{
		ProductRepositoryImpl::change($productChangeDto);
	}

	/**
	 * @throws ProductNotDisabled
	 */
	public static function disableProduct(int $id): void
	{
		ProductRepositoryImpl::disable($id);
	}

	public static function addProduct(ProductAddingDto $productAddingDto): void
	{
		ProductRepositoryImpl::add($productAddingDto);
	}

	/**
	 * @throws ProductNotRestored
	 */
	public static function restoreProduct(int $id): void
	{
		ProductRepositoryImpl::restore($id);
	}

	public static function getColumn()
	{
		$columns = ProductRepositoryImpl::getColumn();
		$columns[] = 'tags';
		return $columns;
	}
}
