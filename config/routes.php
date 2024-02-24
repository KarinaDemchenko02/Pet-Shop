<?php

use Up\Routing\Router;

Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction');
Router::get('/product/:id/', new \Up\Controller\PageDetailController(), 'showProductAction');
Router::post('/logging/', new \Up\Controller\AuthController(), 'authAction');

Router::post('/addToBasket/:id/', new \Up\Controller\BasketController(), 'addProductAction');
Router::post('/deleteFromBasket/:id/', new \Up\Controller\BasketController(), 'deleteProductAction');

Router::get('/admin/', new \Up\Controller\PageAdminController(), 'indexAction');
Router::post('/admin/logIn/', new \Up\Controller\AuthController(), 'logInAdminAction');

Router::patch('/admin/product/disable/', new \Up\Controller\ProductAdminController(), 'disableAction');
Router::patch('/admin/product/restore/', new \Up\Controller\ProductAdminController(), 'restoreAction');
Router::patch('/admin/product/change/', new \Up\Controller\ProductAdminController(), 'changeAction');

Router::delete('/admin/order/', new \Up\Controller\OrderAdminController(), 'deleteAction');
Router::post('/admin/order/add/', new \Up\Controller\OrderAdminController(), 'addAction');
Router::patch('/admin/order/change/', new \Up\Controller\OrderAdminController(), 'changeAction');

Router::delete('/admin/tag/', new \Up\Controller\TagAdminController(), 'deleteAction');
Router::post('/admin/tag/add/', new \Up\Controller\TagAdminController(), 'addAction');
Router::patch('/admin/tag/change/', new \Up\Controller\TagAdminController(), 'changeAction');

Router::post('/product/:id/', new \Up\Controller\PageDetailController(), 'buyProductAction');

Router::get('/success/', new \Up\Controller\PageDetailController(), 'showModalSuccess');

Router::get('/account/', new \Up\Controller\PageAccountController(), 'indexAction');
Router::patch('/account/edit/', new \Up\Controller\ChangeAccountController(), 'changeAction');

Router::post('/upload/', new \Up\Controller\PageAdminController(), 'uploadAction');

Router::post('/createOrder/', new \Up\Controller\OrderController(), 'createOrder');

Router::post('/account/edit/', new \Up\Controller\ChangeController(), 'changeAction');

Router::get('/special-offer/', new \Up\Controller\PageSpecialOfferController(), 'showSpecialOfferAction');
Router::get('/special-offer/:id/', new \Up\Controller\PageProductBySpecialOfferController(), 'showProductBySpecialOfferAction');