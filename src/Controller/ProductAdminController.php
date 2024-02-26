<?php

namespace Up\Controller;

use Up\Dto\ProductAddingDto;
use Up\Dto\ProductChangeDto;
use Up\Exceptions\Admin\ProductNotAdd;
use Up\Exceptions\Admin\ProductNotChanged;
use Up\Exceptions\Admin\ProductNotDisabled;
use Up\Exceptions\Admin\ProductNotRestored;
use Up\Exceptions\Images\ImageNotAdd;
use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Service\ProductService\ProductService;
use Up\Util\Upload;


class ProductAdminController extends Controller
{
	public function disableAction(Request $request): Response
	{
		$id = $request->getDataByKey('id');
		$response = [];
		try
		{
			ProductService::disableProduct((int)$id);
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
			ProductService::restoreProduct((int)$id);
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
			$status = Status::OK;
		}
		else
		{
			$response['errors'] = 'Product not restored';
			$status = Status::NOT_ACCEPTABLE;
		}
		return new Response($status, $response);
	}

	public function changeAction(Request $request): Response
	{
		$productChangeDto = new ProductChangeDto(
			$request->getDataByKey('id'),
			$request->getDataByKey('title'),
			$request->getDataByKey('description'),
			$request->getDataByKey('price'),
			$request->getDataByKey('tags'),
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
			$status = Status::OK;
		}
		else
		{
			$response['errors'] = 'Product not changed';
			$status = Status::NOT_ACCEPTABLE;
		}
		return new Response($status, $response);
	}

	public function addAction(Request $request): Response
	{
		$productAddDto = new ProductAddingDto(
			$request->getDataByKey('title'),
			$request->getDataByKey('description'),
			$request->getDataByKey('price'),
			'/images/imgNotFound.png',
			$request->getDataByKey('tags'),
		);

		$response = [];
		try
		{
			$idProductAdd = ProductService::addProduct($productAddDto);
			$result = true;
		}
		catch (ProductNotAdd)
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

		$response['id'] = $idProductAdd ?? null;


		return new Response($status, $response);
	}

	public function imageAction(Request $request): Response
	{
		$response = [];
		try
		{
			if (($image = $request->getDataByKey('image')) !== null)
			{
				$imagePath = Upload::upload($image);
				ProductService::changeImage($imagePath, $request->getDataByKey('idProduct'));
				$result = true;
			}
			else
			{
				$result = false;
			}
		}
		catch (ImageNotAdd)
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
			$response['errors'] = 'Image not changed';
			$status = Status::NOT_ACCEPTABLE;
		}
		return new Response($status, $response);
	}
}
