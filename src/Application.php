<?php

namespace Up;

use Up\Entity\ShoppingSession;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;

class Application
{
	public function run()
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		session_start();
		if (!isset($_SESSION['user']))
		{
			$_SESSION['user'] = null;
		}
		// if (!isset($_SESSION['shoppingSession']))
		// {
		// 	if ($_SESSION['user'] === null)
		// 	{
		// 		$_SESSION['shoppingSession'] = new ShoppingSession(null, null, [], null, null);
		// 	}
		// 	else
		// 	{
		// 		$_SESSION['shoppingSession'] = ShoppingSessionRepositoryImpl::getByUser($_SESSION['user']->id);
		// 	}
		// }

		Util\Database\Migration::migrate($connection);

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
