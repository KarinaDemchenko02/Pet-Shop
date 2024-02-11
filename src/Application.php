<?php

namespace Up;

use Up\Entity\ShoppingSession;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;

class Application
{
	public function run()
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		Util\Database\Migration::migrate($connection);

		session_start();
		if (!isset($_SESSION['user']))
		{
			$_SESSION['user'] = UserRepositoryImpl::getById(1);
		}
		if (!isset($_SESSION['shoppingSession']))
		{
			if ($_SESSION['user'] === null)
			{
				$_SESSION['shoppingSession'] = new ShoppingSession(null, null, [], null, null);
			}
			else
			{
				$_SESSION['shoppingSession'] = ShoppingSessionRepositoryImpl::getByUser($_SESSION['user']->id);
			}
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
