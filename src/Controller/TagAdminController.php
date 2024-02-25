<?php

namespace Up\Controller;

use Up\Dto\Tag\TagChangingDto;
use Up\Exceptions\Admin\Tag\TagNotChanged;
use Up\Service\TagService\TagService;
use Up\Util\Json;

class TagAdminController extends Controller
{
	public function deleteAction(): void
	{
		/*if (!$this->isLogInAdmin())
		{
			http_response_code(403);
			return;
		}*/

		// $data = Json::decode(file_get_contents("php://input"));
		// $response = [];
		// try
		// {
		// 	TagService::deleteTag((int)$data['id']);
		// 	$result = true;
		// }
		// catch (OrderNotDeleted)
		// {
		// 	$result = false;
		// }
		//
		// $response['result'] = $result;
		//
		// if ($result)
		// {
		// 	$response['errors'] = [];
		// 	http_response_code(200);
		// }
		// else
		// {
		// 	$response['errors'] = 'Order not deleted';
		// 	http_response_code(409);
		// }
		// echo Json::encode($response);
	}

	public function addAction(): void
	{

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
			$dto = new TagChangingDto(
				(int)$data['id'],
				$data['title'],
			);
			TagService::changeTag($dto);
			$result = true;
		}
		catch (TagNotChanged)
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
			$response['errors'] = 'Tag not changed';
			http_response_code(409);
		}
		echo Json::encode($response);
	}
}
