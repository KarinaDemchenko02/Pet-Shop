<?php

use Up\Routing\Router;

\Up\Routing\Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction');
