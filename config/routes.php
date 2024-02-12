<?php

use Up\Routing\Router;

Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction');
Router::get('/product/:id/', new \Up\Controller\PageDetailController(), 'showProductAction');
Router::post('/logging/', new \Up\Controller\AuthController(), 'authAction');

Router::post('/addToBasket/:id/', new \Up\Controller\BasketController(), '');

Router::get('/admin/', new \Up\Controller\PageAdminController(), 'indexAction');
Router::post('/admin/logIn/', new \Up\Controller\AuthController(), 'logInAdminAction');

Router::post('/admin/', new \Up\Controller\MultipleController(), 'processAction');

Router::post('/product/:id/', new \Up\Controller\PageDetailController(), 'buyProductAction');
