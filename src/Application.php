<?php

namespace Up;

use Up\Auth\Auth;
use Up\Dto\UserAddingDto;
use Up\Entity\ShoppingSession;
use Up\Repository\ShoppingSession\ShoppingSessionRepositoryImpl;
use Up\Repository\User\UserRepositoryImpl;
use Up\Util\Session;

class Application
{
	public function run()
	{
		$connection = \Up\Util\Database\Connector::getInstance()->getDbConnection();
		Util\Database\Migration::migrate($connection);

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
