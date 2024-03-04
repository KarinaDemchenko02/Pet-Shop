<?php

namespace Up\Controller;

use Up\Dto\Order\OrderAddingDto;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Service\OrderService\OrderService;
use Up\Util\Session;

class OrderController extends Controller
{
	public function buyProductAction(Request $request): Response
	{
		try
		{
			$orderDto = new OrderAddingDto(
				Session::get('shoppingSession'),
				$request->getDataByKey('name'),
				$request->getDataByKey('surname'),
				$request->getDataByKey('address'),
			);
			OrderService::createOrder($orderDto);
			return new Response(Status::CREATED, ['result' => true, 'redirect' => '/success/']);
		}
		catch (OrderNotCompleted)
		{
			return new Response(Status::BAD_REQUEST, ['result' => true]);
		}
	}

	public function createOrder(Request $request): Response
	{
		$data = $request->getData();
		$shoppingSession = Session::get('shoppingSession');
		try
		{
			$orderDto = new OrderAddingDto(
				$shoppingSession, $data['name'], $data['surname'], $data['address'],
			);
			OrderService::createOrder($orderDto);
			if (!is_null($shoppingSession->id))
			{
				ShoppingSessionRepositoryImpl::delete($shoppingSession->id);
				$shoppingSession = ShoppingSessionRepositoryImpl::getByUser($shoppingSession->userId);
				Session::set("shoppingSession", $shoppingSession);
			}
			else
			{
				Session::unset('shoppingSession');
			}
			return new Response(Status::CREATED, ['result' => true,]);
		}
		catch (OrderNotCompleted)
		{
			return new Response(Status::BAD_REQUEST);
		}
	}
}
