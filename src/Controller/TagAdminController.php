<?php

namespace Up\Controller;

use Up\Dto\Tag\TagChangingDto;
use Up\Exceptions\Admin\Tag\TagNotChanged;
use Up\Exceptions\Tag\TagNotAdding;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
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

	public function addAction(Request $request): Response
	{

		$title = $request->getDataByKey('title');

		$response = [];
		try
		{
			$idUserAdd = TagService::addTag($title);
			$result = true;
		}
		catch (TagNotAdding)
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
			$response['errors'] = 'Product not added';
			$status = Status::NOT_ACCEPTABLE;
		}

		$response['id'] = $idUserAdd ?? null;

		return new Response($status, $response);
	}

	public function changeAction(Request $request): Response
	{
		$data = $request->getData();
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
			$status = Status::OK;
		}
		else
		{
			$response['errors'] = 'Tag not changed';
			$status = Status::BAD_REQUEST;
		}
		return new Response($status, $response);
	}

	public function getTagAdminJsonAction(Request $request): Response
	{
		$page = $request->getVariable('page');

		if (!(is_numeric($page) && $page > 0))
		{
			$page = 1;
		}

		$tags = TagService::getAllProductsForAdmin($page);
		$nextPage = TagService::getAllProductsForAdmin($page + 1);

		return new Response(Status::OK, [
			'tags' => $tags,
			'nextPage' => $nextPage,
		]);
	}
}
