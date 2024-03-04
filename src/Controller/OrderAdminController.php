<?php

namespace Up\Controller;

use Up\Dto\Order\OrderAddingAdminDto;
use Up\Dto\Order\OrderChangingDto;
use Up\Dto\Product\ProductAddingDto;
use Up\Exceptions\Admin\Order\OrderNotChanged;
use Up\Exceptions\Admin\Order\OrderNotDeleted;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Service\OrderService\OrderService;
use Up\Util\Json;

class OrderAdminController extends Controller
{
	public function addAction(Request $request): Response
	{
		$data = $request->getData();

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
			$status = Status::OK;
		}
		else
		{
			$response['errors'] = 'Order not added';
			$status = Status::BAD_REQUEST;
		}
		return new Response($status, $response);
	}
	public function deleteAction(Request $request): Response
	{
		$data = $request->getData();
		$response = [];
		try
		{
			OrderService::disableOrder((int)$data['id']);
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
			$status = Status::OK;
		}
		else
		{
			$response['errors'] = 'Order not deleted';
			$status = Status::BAD_REQUEST;
		}
		return new Response($status, $response);
	}

	public function changeAction(Request $request): Response
	{
		$data = $request->getData();
		$response = [];
		try
		{
			$orderDto = new OrderChangingDto(
				(int)$data['id'],
				$data['deliveryAddress'],
				$data['name'],
				$data['surname'],
				$data['statusId']
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
			$status = Status::OK;
		}
		else
		{
			$response['errors'] = 'Order not changed';
			$status = Status::BAD_REQUEST;
		}
		return new Response($status, $response);
	}
}
