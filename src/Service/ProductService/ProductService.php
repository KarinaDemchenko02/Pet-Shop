<?php

namespace Up\Service\ProductService;

use Up\Dto\ProductDto;
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
}
