<?php

namespace Up\Controller;

use Up\Dto\Order\OrderAddingDto;
use Up\Exceptions\Service\OrderService\OrderNotCompleted;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Service\OrderService\OrderService;
use Up\Util\Session;

class OrderController extends BaseController
{
	public function buyProductAction(int $id)
	{
		try
		{
			if ($this->isLogIn())
			{
				$userId = Session::get('user')->id;
			}
			else
			{
				$userId = null;
			}

			$orderDto = new OrderAddingDto(
				$userId, $_POST['name'], $_POST['surname'], $_POST['address'],

			);
			OrderService::createOrder($orderDto);
			header('Location: /success/');
		}
		catch (OrderNotCompleted)
		{
			echo "fail";
		}
	}

	public function createOrder()
	{
		$shoppingSession = Session::get('shoppingSession');
		try
		{
			$orderDto = new OrderAddingDto(
				$shoppingSession, 'Мем', 'Прикол', 'Я схожу с ума',
			);
			OrderService::createOrder($orderDto);
			if (!is_null($shoppingSession->id))
			{
				ShoppingSessionRepositoryImpl::delete($shoppingSession->id);
			}
			Session::set('shoppingSession', null);
			header('Location: /success/');
		}
		catch (OrderNotCompleted)
		{
			echo "fail";
		}
	}
}
