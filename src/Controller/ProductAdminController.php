<?php

namespace Up\Controller;

use Up\Dto\ProductChangeDto;
use Up\Exceptions\Admin\ProductNotChanged;
use Up\Exceptions\Admin\ProductNotDisabled;
use Up\Exceptions\Admin\ProductNotRestored;
use Up\Service\ProductService\ProductService;
use Up\Util\Json;

class ProductAdminController extends BaseController
{
	public function disableAction(): void
	{
		/*if (!$this->isLogInAdmin())
		{
			http_response_code(403);
			return;
		}*/
		$data = Json::decode(file_get_contents("php://input"));
		$response = [];
		try
		{
			ProductService::disableProduct((int)$data['id']);
			$result = true;
		}
		catch (ProductNotDisabled)
		{
			$result = false;
		}

		$response['result'] = $result;

		if ($result)
		{
			$response['errors'] = [];
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'Product not disabled';
			http_response_code(409);
		}
		echo Json::encode($response);
	}

	public function restoreAction(): void
	{
		/*if (!$this->isLogInAdmin())
		{
			http_response_code(403);
			return;
		}*/
		$data = Json::decode(file_get_contents("php://input"));

		$response = [];
		try
		{
			ProductService::restoreProduct((int)$data['id']);
			$result = true;
		}
		catch (ProductNotRestored)
		{
			$result = false;
		}

		$response['result'] = $result;

		if ($result)
		{
			$response['errors'] = [];
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'Product not restored';
			http_response_code(409);
		}
		echo Json::encode($response);
	}

	public function changeAction(): void
	{
		/*if (!$this->isLogInAdmin())
		{
			http_response_code(403);
			return;
		}*/
		$data = Json::decode(file_get_contents("php://input"));

		$productChangeDto = new ProductChangeDto(
			$data['id'],
			$data['title'],
			$data['description'],
			$data['price'],
			'/images/imgNotFound.png',
		);


		$response = [];
		try
		{
			ProductService::changeProduct($productChangeDto);
			$result = true;
		}
		catch (ProductNotChanged)
		{
			$result = false;
		}

		$response['result'] = $result;

		if ($result)
		{
			$response['errors'] = [];
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'Product not changed';
			http_response_code(409);
		}
		echo Json::encode($response);
	}
}
