<?php

namespace Up\Controller;

use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Service\OrderService\OrderService;
use Up\Service\UserService\UserService;
use Up\Util\TemplateEngine\PageAccountTemplateEngine;

class PageAccountController extends Controller
{
	public function __construct()
	{
		$this->engine = new PageAccountTemplateEngine();
	}

	public function indexAction(Request $request): Response
	{
		if ($request->getDataByKey('email') !== null)
		{
			return $this->accountAction($request);
		}

		return $this->logInAction($request);
	}

	private function logInAction(Request $request): Response
	{
		return new Response(Status::UNAUTHORIZED, ['template' => $this->engine->getAuthPageTemplate([
			'isLogIn' => false,
			'destination' => '/account/logging/'
		])]);
	}

	public function accountAction(Request $request): Response
	{
		$userId = $request->getDataByKey('userId');

		$user = UserService::getUserById($userId);
		$orders = OrderService::getOrderByUser($userId);

		$dataUser = [
			'id' => $user->id,
			'name' => $user->name,
			'surname' => $user->surname,
			'email' => $user->email,
			'role' => $user->roleTitle,
			'phoneNumber' => $user->phoneNumber
		];

		$template = $this->engine->getPageTemplate([
			'isLogIn' => $this->isLogIn(),
			'user' => $dataUser,
			'orders' => $orders
		]);

		return new Response(Status::OK, ['template' => $template]);
	}
	public function signUpAction(Request $request): Response
	{
		$response = (new AuthController())->authAction($request);
		$response->setRedirect('/account/');
		return $response;
	}
}
