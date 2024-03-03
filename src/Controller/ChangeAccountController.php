<?php

namespace Up\Controller;

use Up\Exceptions\Auth\InvalidPassword;
use Up\Exceptions\User\UserNotFound;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Service\UserService\UserService;

class ChangeAccountController extends Controller
{
	public function changeAction(Request $request): Response
	{
		$data = $request->getData();

		$response = [];
		$response['errors'] = [];

		try
		{
			UserService::changeUser((int) $data['id'], $data['name'], $data['surname'], $data['email'], $data['phoneNumber'], $data['password']);
			$result = true;
		}
		catch (UserNotFound)
		{
			$result = false;
			$response['errors'][] = 'Пользователь не найден';
		}
		catch (InvalidPassword)
		{
			$result = false;
			$response['errors'][] = 'Пароль неверный';
		}

		$response['result'] = $result;

		if ($response['result'])
		{
			$response['errors'] = [];
			$status = Status::OK;
		}
		else
		{
			$response['errors'][] = 'Данные не изменены';
			$status = Status::BAD_REQUEST;
		}
		return new Response($status, $response);
	}
}
