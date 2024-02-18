<?php

namespace Up\Util\TemplateEngine;

class PageAccountTemplateEngine implements TemplateEngine
{
	public function getPageTemplate($variables): Template
	{
		$user = $variables['user'];
		$isLogIn = $variables['isLogIn'];
		$header = $this->getHeaderTemplate($isLogIn);
		$footer = new Template('components/main/footer');
		$userInfo = new Template('components/account/pageUserInfo', [
			'user' => $user
		]);


		return new Template('page/account/account', [
			'header' => $header,
			'content' => $userInfo,
			'footer' => $footer,
		]);
	}

	public function getAuthPageTemplate($variables): Template
	{
		$isLogIn = $variables['isLogIn'];
		$header = $this->getHeaderTemplate($isLogIn);
		$footer = new Template('components/main/footer');
		$content = new Template('components/main/formAuthorization');
		return (new Template('page/account/account', [
			'header' => $header,
			'content' => $content,
			'footer' => $footer
		]));
	}

	public function getHeaderTemplate(bool $isLogIn): Template
	{
		if ($isLogIn)
		{
			$authSection = new Template('components/main/logOut');
		}
		else
		{
			$authSection = new Template('components/main/logIn');
		}
		return new Template('components/main/header', ['authSection' => $authSection]);
	}

}