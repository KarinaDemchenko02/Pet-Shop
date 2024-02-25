<?php

namespace Up;

use Up\Entity\ShoppingSession;
use Up\Repository\Product\ProductRepositoryImpl;
use Up\Util\Compression\CompressionImages;
use Up\Util\Session;
use Up\Util\Upload;

class Application
{
	public function run()
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		//Util\Database\Migration::migrate($connection);

		$imagesCompressed = new CompressionImages(
			ROOT . '/public/images/',
			ROOT . '/public/compressImages/');

		$imagesCompressed->compressImages();

		Session::init();
		$user = Session::get('user');
		$shoppingSession = Session::get('shoppingSession');

		if (!isset($shoppingSession) && is_null($user))
		{
			Session::set('shoppingSession', new ShoppingSession(null, null, []));
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
