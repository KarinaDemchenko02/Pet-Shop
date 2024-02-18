<?php

use Up\Routing\Router;

Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction');
Router::get('/product/:id/', new \Up\Controller\PageDetailController(), 'showProductAction');
Router::post('/logging/', new \Up\Controller\AuthController(), 'authAction');

Router::post('/addToBasket/:id/', new \Up\Controller\BasketController(), 'addProductAction');
Router::post('/deleteFromBasket/:id/', new \Up\Controller\BasketController(), 'deleteProductAction');

Router::get('/admin/', new \Up\Controller\PageAdminController(), 'indexAction');
Router::post('/admin/logIn/', new \Up\Controller\AuthController(), 'logInAdminAction');

Router::patch('/admin/product/disable/', new \Up\Controller\PageAdminController(), 'disableAction');
Router::patch('/admin/product/restore/', new \Up\Controller\PageAdminController(), 'restoreAction');
Router::patch('/admin/product/change/', new \Up\Controller\PageAdminController(), 'changeAction');

Router::post('/product/:id/', new \Up\Controller\PageDetailController(), 'buyProductAction');

Router::get('/success/', new \Up\Controller\PageDetailController(), 'showModalSuccess');

Router::get('/account/', new \Up\Controller\PageAccountController(), 'indexAction');
Router::post('/account/edit/', new \Up\Controller\ChangeController(), 'changeAction');
Router::post('/upload/', new \Up\Controller\PageAdminController(), 'uploadAction');

Router::post('/createOrder/', new \Up\Controller\OrderController(), 'createOrder');
