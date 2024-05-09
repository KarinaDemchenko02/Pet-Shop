<?php

namespace Up\Util\TemplateEngine;

class PageAccountTemplateEngine implements TemplateEngine
{
	public function getPageTemplate($variables): Template
	{
		$user = $variables['user'];
		$orders = $variables['orders'];
		$header = new Template('components/account/header');
		$footer = new Template('components/main/footer');
		$userInfo = new Template('components/account/pageUserInfo', [
			'user' => $user,
			'orders' => $orders
		]);


		return new Template('page/account/account', [
			'header' => $header,
			'content' => $userInfo,
			'footer' => $footer,
		]);
	}
}
