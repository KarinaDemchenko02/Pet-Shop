<?php

namespace Up\Controller;

class PageMainController extends BaseController
{
	public function showProductsAction()
	{
		$this->render('page/main/main.php', [
			'title' => 'petshop',
			'description' => 'something',
		]);
	}
}
