<?php

namespace Up\Controller;

use Up\Dto\Order\OrderAddingDto;
use Up\Exceptions\Order\OrderNotCompleted;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Service\OrderService\OrderService;
use Up\Util\Json;
use Up\Util\Session;

class OrderController extends Controller
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

	public function createOrder(Request $request): Response
	{
		$data = Json::decode(file_get_contents("php://input"));

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
		}
		catch (OrderNotCompleted)
		{
			echo "fail";
		}

		return new Response(Status::OK, ['result' => true]);
	}
}
