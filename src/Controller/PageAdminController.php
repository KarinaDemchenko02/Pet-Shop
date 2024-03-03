<?php

namespace Up\Controller;

use Up\Http\Request;
use Up\Http\Response;
use Up\Http\Status;
use Up\Entity\ProductQuantity;
use Up\Service\OrderService\OrderService;
use Up\Service\ProductService\ProductService;
use Up\Service\TagService\TagService;
use Up\Service\UserService\UserService;
use Up\Util\Database\Tables\ProductTable;
use Up\Util\Database\Tables\TagTable;
use Up\Util\Database\Tables\UserTable;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;

class PageAdminController extends Controller
{
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
	}

	public function logInAction(Request $request): Response
	{
		return new Response(Status::OK, ['template' => $this->engine->getAuthPageTemplate()]);
	}

	public function showProductsAction(Request $request): Response
	{
		$contentName = 'products';
		$entity = $request->getVariable('entity') ?? 'products';
		$page = (int)($request->getVariable('page') ?? 1);

		$content = [];

		if ($entity === 'users')
		{
			$contentName = 'users';
			$users = UserService::getAll();
			$columns = UserService::getColumn();
			foreach ($users as $user)
			{
				$content[] = [
					'id' => $user->id,
					'email' => $user->email,
					'password' => $user->password,
					'roleTitle' => $user->roleTitle,
					'phoneNumber' => $user->phoneNumber,
				];
			}
		}
		elseif ($entity === 'tags')
		{
			$contentName = 'tags';
			$tags = TagService::getAllTags();
			$columns = TagService::getColumn();
			foreach ($tags as $tag)
			{
				$content[] = [
					'id' => $tag->id,
					'title' => $tag->title,
				];
			}
		}
		elseif ($entity === 'orders')
		{
			$contentName = 'orders';
			$orders = OrderService::getAllOrder();
			$columns = OrderService::gelColumn();
			foreach ($orders as $order)
			{
				$products = [];
				foreach ($order->products as $product)
				{
					/** @var ProductQuantity $product */
					$products[] = [
						'itemId' => $product->info->id,
						'quantities' => $product->getQuantity(),
						'price' => $product->info->price,
					];
				}
				$content[] = [
						'id' => $order->id,
						'products' => $products,
						'userId' => $order->userId,
						'deliveryAddress' => $order->deliveryAddress,
						'createdAt' => $order->createdAt,
						'editedAt' => $order->editedAt,
						'name' => $order->name,
						'surname' => $order->surname,
						'status' => $order->status,
					];
			}
		}
		else
		{
			$products = ProductService::getAllProductsForAdmin($page);
			$columns = ProductService::getColumn();

			$allTags = TagService::getAllTags();

			$tagsArray = [];

			foreach ($allTags as $tag)
			{
				$tagsArray[] = [
					'id' => $tag->id,
					'title' => $tag->title
				];
			}

			foreach ($products as $product)
			{
				$tags = [];
				foreach ($product->tags as $tag)
				{
					$tags[] = [
						'tagId' => $tag->id,
						'tagTitle' => $tag->title,
					];
				}
				$content[] =
					[
						'title' => $product->title,
						'description' => $product->description,
						'price' => $product->price,
						'id' => $product->id,
						'imagePath' => $product->imagePath,
						'isActive' => $product->isActive ? 'Да' : 'Нет',
						'priority' => $product->priority,
						'addedAt' => $product->addedAt,
						'editedAt' => $product->editedAt,
						'tags' => $tags,
					];
			}
		}

		$template = $this->engine->getPageTemplate([
			'contentName' => $contentName,
			'content' => $content,
			'columns' => $columns,
			'tag' => $tagsArray ?? []
		]);

		return new Response(Status::OK, ['template' => $template]);
	}
}
