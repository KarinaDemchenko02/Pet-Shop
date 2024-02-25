<?php

namespace Up\Controller;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Exceptions\Admin\ProductNotChanged;
use Up\Exceptions\Admin\ProductNotDisabled;
use Up\Exceptions\Admin\ProductNotRestored;
use Up\Exceptions\Images\ImageNotAdd;
use Up\Http\Request;
use Up\Http\Response;
use Up\Service\ProductService\ProductService;
use Up\Util\Json;
use Up\Util\Upload;

class ProductAdminController extends Controller
{
	public function disableAction(Request $request): Response
	{
		$data = Json::decode(file_get_contents("php://input"));
		$response = [];
		try
		{
			ProductService::disableProduct((int)$data['id']);
			$result = true;
		}
		catch (ProductNotDisabled)
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
			$response['errors'] = 'Product not disabled';
			http_response_code(409);
		}
		echo Json::encode($response);
	}

	public function restoreAction(Request $request): Response
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
			ProductService::restoreProduct((int)$data['id']);
			$result = true;
		}
		catch (ProductNotRestored)
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
			$response['errors'] = 'Product not restored';
			http_response_code(409);
		}
		echo Json::encode($response);
	}

	public function changeAction(Request $request): Response
	{
		$data = Json::decode(file_get_contents("php://input"));

		$productChangeDto = new ProductChangeDto(
			$data['id'],
			$data['title'],
			$data['description'],
			$data['price'],
			$data['tags'],
			'/images/imgNotFound.png',
		);

		$response = [];
		try
		{
			ProductService::changeProduct($productChangeDto);
			$result = true;
		}
		catch (ProductNotChanged)
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
			$response['errors'] = 'Product not changed';
			http_response_code(409);
		}
		echo Json::encode($response);
	}

	public function addAction(Request $request): Response
	{
		$data = Json::decode(file_get_contents("php://input"));

		$productAddDto = new ProductAddingDto(
			$data['title'],
			$data['description'],
			$data['price'],
			'/images/imgNotFound.png',
			$data['tags'],
		);

		$response = [];
		try
		{
			$idProductAdd = ProductService::addProduct($productAddDto);
			$result = true;
		}
		catch (ProductNotChanged)
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
			$response['errors'] = 'Product not changed';
			http_response_code(409);
		}
		echo Json::encode([
			'id' => $idProductAdd ?? null
		]);
	}

	public function imageAction(Request $request): Response
	{
		$response = [];
		try
		{
			if (isset($_FILES['imagePath']))
			{
				Upload::upload();
			}
			ProductService::addImage($_FILES['imagePath']['name'], $_POST['idProduct']);
			$result = true;
		}
		catch (ImageNotAdd)
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
			$response['errors'] = 'Product not changed';
			http_response_code(409);
		}

		echo Json::encode($response);
	}
}
