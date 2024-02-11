<?php

namespace Up\AdminAction;

use Up\Exceptions\Service\AdminService\ProductNotDisable;
use Up\Service\ProductService\ProductService;

class Disable
{
	private array $errors = [];
	public function disableProduct(int $id): bool
	{
		try {
			ProductService::disableProduct($id);
			return true;
		}
		catch (ProductNotDisable $exception)
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