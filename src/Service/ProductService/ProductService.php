<?php

namespace Up\Service\ProductService;

use Up\Dto\Dto;
use Up\Dto\ProductDto;
use Up\Entity\Product;
use Up\Repository\ProductRepository\ProductRepositoryImpl;


class ProductService
{
	public static function getAllProducts(): array
	{

		$products = ProductRepositoryImpl::getAll();
//		$products =  [(new Product(1, 'title', 'desc', 1000, [1, 2], true, 'addedAt', 'editedAt')),
//			(new Product(2, 'title2', 'desc2', 1000, [1, 2], true, 'addedAt', 'editedAt'))];
		//
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
}
