<?php

use Up\Routing\Router;

\Up\Routing\Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction');
\Up\Routing\Router::get('/product/:id/', new \Up\Controller\PageDetailController(), 'showProductAction');
\Up\Routing\Router::get('/admin/', new \Up\Controller\PageAdminController(), 'showProductsAction');
