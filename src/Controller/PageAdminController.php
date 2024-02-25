<?php

namespace Up\Controller;

use Up\Service\OrderService\OrderService;
use Up\Service\ProductService\ProductService;
use Up\Service\TagService\TagService;
use Up\Service\UserService\UserService;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;
use Up\Util\Upload;

class PageAdminController extends BaseController
{
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
	}

	public function indexAction()
	{
		if ($this->isLogInAdmin())
		{
			$this->showProductsAction();
		}
		else
		{
			$this->logInAction();
		}
	}

	public function uploadAction(): void
	{
		Upload::upload();
		$this->indexAction();
	}
	private function logInAction()
	{
		$this->engine->getAuthPageTemplate()->display();
	}

	public function showProductsAction()
	{
		$contentName = 'products';
		$entity = $_GET['entity'] ?? 'products';
		$content = [];
		$page = 1;
		if (isset($_GET['page']))
		{
			$page = (int)$_GET['page'];
		}
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
		elseif ($entity === 'tag')
		{
			$contentName = 'tag';
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
					$products[] = [
						'itemId' => $product->id,
						'quantities' => $product->quantity,
						'price' => $product->price,
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
						'isActive' => (int) $product->isActive,
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

		$template->display();
	}
}
