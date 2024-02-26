<?php

use Up\Routing\Router;

Router::group(['preMiddleware' => 'isLogin'], [
	Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction'),
	Router::get('/product/:id/', new \Up\Controller\PageDetailController(), 'showProductAction'),
	Router::post('/addToBasket/:id/', new \Up\Controller\BasketController(), 'addProductAction'),
	Router::get('/products-json/', new \Up\Controller\PageMainController(), 'getProductsJsonAction'),
	Router::get('/tags-json/', new \Up\Controller\PageMainController(), 'getTagsJsonAction'),
	Router::get('/search-json/', new \Up\Controller\PageMainController(), 'getSearchJsonAction'),
	Router::delete('/deleteFromBasket/:id/', new \Up\Controller\BasketController(), 'deleteProductAction'),
]);

Router::group(['preMiddleware' => 'isNotLogIn'], [
	Router::get('/admin/logIn/', new \Up\Controller\PageAdminController(), 'logInAction'),
	Router::post('/logging/', new \Up\Controller\AuthController(), 'authAction'),
	Router::post('/admin/logIn/', new \Up\Controller\AuthController(), 'logInAdminAction'),
]);


Router::group(['preMiddleware' => ['isLogin', 'isAdmin']/*, 'postMiddleware' => 'test'*/], [
	Router::get('/admin/', new \Up\Controller\PageAdminController(), 'showProductsAction'),

	Router::patch('/admin/product/disable/', new \Up\Controller\ProductAdminController(), 'disableAction'),
	Router::patch('/admin/product/restore/', new \Up\Controller\ProductAdminController(), 'restoreAction'),
	Router::patch('/admin/product/change/', new \Up\Controller\ProductAdminController(), 'changeAction'),
	Router::post('/admin/product/image/', new \Up\Controller\ProductAdminController(), 'imageAction'),
	Router::post('/admin/product/add/', new \Up\Controller\ProductAdminController(), 'addAction'),

	Router::delete('/admin/order/', new \Up\Controller\OrderAdminController(), 'deleteAction'),
	Router::post('/admin/order/add/', new \Up\Controller\OrderAdminController(), 'addAction'),
	Router::patch('/admin/order/change/', new \Up\Controller\OrderAdminController(), 'changeAction'),

	Router::delete('/admin/tag/', new \Up\Controller\TagAdminController(), 'deleteAction'),
	Router::post('/admin/tag/add/', new \Up\Controller\TagAdminController(), 'addAction'),
	Router::patch('/admin/tag/change/', new \Up\Controller\TagAdminController(), 'changeAction'),
]);


Router::post('/product/:id/', new \Up\Controller\PageDetailController(), 'buyProductAction');

Router::get('/success/', new \Up\Controller\PageDetailController(), 'showModalSuccess');

Router::get('/account/', new \Up\Controller\PageAccountController(), 'indexAction');
Router::patch('/account/edit/', new \Up\Controller\ChangeAccountController(), 'changeAction');

Router::post('/createOrder/', new \Up\Controller\OrderController(), 'createOrder');
