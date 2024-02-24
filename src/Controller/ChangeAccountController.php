<?php

namespace Up\Controller;

use Up\Exceptions\Auth\InvalidPassword;
use Up\Exceptions\User\UserNotFound;
use Up\Service\UserService\UserService;
use Up\Util\Json;

class ChangeAccountController extends BaseController
{
	public function changeAction(): void
	{
		$data = Json::decode(file_get_contents("php://input"));

		/*if (!$this->isLogIn() || !($this->getUser()->id === (int)$data['id']))
		{
			return;
		}*/

		$response = [];
		$response['errors'] = [];

		try
		{
			UserService::changeUser((int) $data['id'], $data['name'], $data['email'], $data['phoneNumber'], $data['password']);
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
			http_response_code(200);
		}
		else
		{
			$response['errors'] = 'Товар не удалён';
			http_response_code(400);
		}
		echo Json::encode($response);
	}
}
