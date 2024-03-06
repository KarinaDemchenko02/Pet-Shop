<?php

namespace Up\Controller;

use Up\Exceptions\User\UserNotDisabled;
use Up\Exceptions\User\UserNotRestored;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Service\ProductService\ProductService;
use Up\Service\UserService\UserService;

class UserAdminController extends Controller
{
	public function disableAction(Request $request): Response
	{
		$id = $request->getDataByKey('id');
		$response = [];
		try
		{
			UserService::disableUser($id);
			$result = true;
		}
		catch (UserNotDisabled)
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
			$response['errors'] = 'Product not disabled';
			$status = Status::NOT_ACCEPTABLE;
		}
		return new Response($status, $response);
	}

	public function restoreAction(Request $request): Response
	{
		$id = $request->getDataByKey('id');

		$response = [];
		try
		{
			UserService::restoreUser($id);
			$result = true;
		}
		catch (UserNotRestored)
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
			$response['errors'] = 'Product not restored';
			$status = Status::NOT_ACCEPTABLE;
		}
		return new Response($status, $response);
	}

	public function getUserAdminJsonAction(Request $request): Response
	{
		$page = $request->getVariable('page');

		if (!(is_numeric($page) && $page > 0))
		{
			$page = 1;
		}

		$users = UserService::getAllProductsForAdmin($page);
		$nextPage = UserService::getAllProductsForAdmin($page + 1);

		return new Response(Status::OK, [
			'users' => $users,
			'nextPage' => $nextPage,
		]);
	}
}