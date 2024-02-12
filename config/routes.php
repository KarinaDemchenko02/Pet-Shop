<?php

use Up\Routing\Router;

Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction');
Router::get('/product/:id/', new \Up\Controller\PageDetailController(), 'showProductAction');
Router::post('/', new \Up\Controller\AuthController(), 'authAction');

Router::get('/admin/', new \Up\Controller\PageAdminController(), 'showProductsAction');

Router::post('/admin/', new \Up\Controller\MultipleController(), 'processAction');

Router::post('/product/:id/', new \Up\Controller\PageDetailController(), 'buyProductAction');
