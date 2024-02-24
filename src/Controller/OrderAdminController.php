<?php

namespace Up\Controller;

use Up\Dto\Order\OrderAddingAdminDto;
use Up\Dto\Order\OrderChangingDto;
use Up\Dto\Product\ProductAddingDto;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Admin\Order\OrderNotDeleted;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Service\OrderService\OrderService;
use Up\Util\Json;

class OrderAdminController extends BaseController
{
	public function addAction(): void
	{
		/*if (!$this->isLogInAdmin())
		{
			http_response_code(403);
			return;
		}*/

		$data = Json::decode(file_get_contents("php://input"));

		$productsDto = [];
		foreach ($data['products'] as $product)
		{
			$productsDto[] = new ProductAddingDto(
				$product['id'],
				$product['quantity'],
				$product['price'],
			);
		}

		$orderDto = new OrderAddingAdminDto(
			$productsDto,
			$data['deliveryAddress'],
			$data['name'],
			$data['surname'],
		);

		$response = [];
		try
		{
			OrderService::createOrder($orderDto);
			$result = true;
		}
		catch (OrderNotCompleted)
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
			$response['errors'] = 'Order not added';
			http_response_code(409);
		}
		echo Json::encode($response);
	}
	public function deleteAction(): void
	{
		if (!$this->isLogInAdmin())
		{
			http_response_code(403);
			return;
		}

		$data = Json::decode(file_get_contents("php://input"));
		$response = [];
		try
		{
			OrderService::deleteOrder((int)$data['id']);
			$result = true;
		}
		catch (OrderNotDeleted)
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
			$response['errors'] = 'Order not deleted';
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
		$response = [];
		try
		{
			$orderDto = new OrderChangingDto(
				(int)$data['id'],
				$data['deliveryAddress'],
				$data['name'],
				$data['surname'],
			);
			OrderService::changeOrder($orderDto);
			$result = true;
		}
		catch (OrderNotChanged)
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
			$response['errors'] = 'Order not changed';
			http_response_code(409);
		}
		echo Json::encode($response);
	}
}
