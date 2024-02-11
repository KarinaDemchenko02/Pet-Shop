<?php

namespace Up\AdminAction;

use Up\Dto\ProductAddingDto;
use Up\Exceptions\Service\AdminService\ProductNotAdd;
use Up\Service\ProductService\ProductService;

class Add {
	private array $errors = [];
	public function addProduct(string $title, string $description, string $price): bool
	{
		$productAddingDto = new ProductAddingDto($title, $description, $price);

		try
		{
			ProductService::addProduct($productAddingDto);
			return true;
		}
		catch (ProductNotAdd $exception)
		{
			$this->errors[] = $exception->getMessage();
			return false;
		}
	}

	public function getErrors(): array
	{
		return $this->errors;
	}
}