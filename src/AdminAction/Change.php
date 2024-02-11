<?php

namespace Up\AdminAction;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Entity\Product;
use Up\Exceptions\Service\AdminService\ProductNotChanged;
use Up\Service\ProductService\ProductService;

class Change
{
	private array $errors = [];

	public function changeProduct(int $id, string $title, string $description, $price): bool
	{

		$productChangeDto = new ProductChangeDto($id, $title, $description, $price);

		try {
			ProductService::changeProduct($productChangeDto);
			return true;
		}
		catch (ProductNotChanged $exception)
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