<?php

namespace Up\Controller;

use Up\Exceptions\User\UserNotDisabled;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Service\ProductService\ProductService;

class UserAdminController extends Controller
{
	public function disableAction(Request $request): Response
	{
		$id = $request->getDataByKey('id');
		$response = [];
		try
		{
			//ProductService::disableProduct((int)$id);
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
}