<?php

namespace Up;

use Up\Entity\ShoppingSession;
use Up\Util\Session;

class Application
{
	public function run()
	{
		Util\Database\Migration::migrate();

		$imagesCompressed = new Util\Compression\CompressionImages
		(
			ROOT . '/public/images/',
			ROOT . '/public/compressImages/'
		);
		$imagesCompressed->compressImages();

		Session::init();
		$user = Session::get('user');
		$shoppingSession = Session::get('shoppingSession');

		if (!isset($shoppingSession) && is_null($user))
		{
			Session::set('shoppingSession', new ShoppingSession(null, null, [], null, null));
		}

		$route = \Up\Routing\Router::find($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

		if ($route)
		{
			$action = $route->action;
			$variables = $route->getVariables();
			echo $action(...$variables);
		}
		else
		{
			http_response_code(404);
			echo "Page not found";
			exit;
		}
	}
}
