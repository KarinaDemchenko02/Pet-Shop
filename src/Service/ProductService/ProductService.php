<?php

namespace Up\Service\ProductService;

use Up\Dto\ProductDto;
use Up\Entity\Product;


class ProductService
{
	public static function getAllProducts(): array
	{
		// Обращаемся к репозиторию -> репозиторий возвращает данные
		$products =  [(new Product(1, 'title', 'desc', 1000, [1, 2], true, 'addedAt', 'editedAt')),
			(new Product(2, 'title2', 'desc2', 1000, [1, 2], true, 'addedAt', 'editedAt'))];
		//
		$productsDto = [];
		foreach ($products as $product)
		{
			$productsDto[] = new ProductDto($product);
		}

		return $productsDto;
	}
}
