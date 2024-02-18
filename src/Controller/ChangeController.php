<?php

namespace Up\Controller;

use Up\AdminAction\Change;
use Up\Dto\UserDto;
use Up\Exceptions\Service\UserService\UserNotFound;
use Up\Service\UserService\UserService;
use Up\Util\Json;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;

class ChangeController extends BaseController
{
//	private Change $changeService;
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
//		$this->changeService = new Change();
	}

	public function changeAction(): void
	{
		$data = Json::decode(file_get_contents("php://input"));
		$response = [];

		try
		{
			UserService::changeUser((int) $data['id'], $data['name'], $data['email'], $data['phoneNumber'], $data['password']);
			$result = true;
		}
		catch (UserNotFound)
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
			$response['errors'] = 'Товар не удалён';
			http_response_code(400);
		}
		echo Json::encode([
			'result' => 'Y'
		]);
	}
}