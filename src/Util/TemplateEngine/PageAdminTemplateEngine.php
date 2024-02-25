<?php

namespace Up\Util\TemplateEngine;

class PageAdminTemplateEngine implements TemplateEngine
{
	public function getPageTemplate(array $variables): Template
	{
		$header = new Template('components/admin/header');
		$form = new Template('components/admin/form');
		$delete = new Template('components/admin/delete');

		$content = new Template('components/admin/table', [
			'content' => $variables['content'],
			'columns' => $variables['columns'],
			'tag' => $variables['tag'],
			'contentName' => $variables['contentName'],
			'form' => $form,
			'delete' => $delete
		]);

		return (new Template('page/admin/admin', [
			'header' => $header,
			'content' => $content
		]));
	}
	public function getAuthPageTemplate(): Template
	{
		$header = new Template('components/admin/header');
		$content = new Template('components/admin/formAuthorization');
		return (new Template('page/admin/admin', [
			'header' => $header,
			'content' => $content,
		]));
	}
	public function getProductsSectionTemplate(array $products): array
	{
		$productTemplates = [];
		foreach ($products as $product)
		{
			$productTemplates[] = new Template('components/admin/table',
				[
					'title' => $product->title,
					'desc' => $product->description,
					'price' => $product->price,
					'id' => $product->id,
					'isActive' => (int) $product->isActive,
					'addedAt' => $product->addedAt,
					'editedAt' => $product->editedAt
				]
			);
		}

		return $productTemplates;
	}

	public function getOrdersSectionTemplate(array $orders): array
	{
		$orderTemplates = [];
		foreach ($orders as $order)
		{
			$orderTemplates[] = new Template('components/admin/dataTableOrders',
				[
					'userId' => $order->userId,
					'name' => $order->name,
					'surname' => $order->surname,
					'address' => $order->deliveryAddress,
					'productId' => $order->productId,
					'statusId' => $order->statusId,
					'createdAt' => $order->createdAt
				]
			);
		}

		return $orderTemplates;
	}

}
