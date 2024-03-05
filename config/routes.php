<?php

use Up\Routing\Router;

Router::group(['preMiddleware' => 'isLogin'], [
	Router::get('/', new \Up\Controller\PageMainController(), 'showProductsAction'),
	Router::get('/product/:id/', new \Up\Controller\PageDetailController(), 'showProductAction'),
	Router::get('/special-offer/', new \Up\Controller\PageSpecialOfferController(), 'showSpecialOfferAction'),
	Router::get('/special-offer/:id/', new \Up\Controller\PageProductBySpecialOfferController(), 'showProductBySpecialOfferAction'),
	]);

Router::post('/addToBasket/:id/', new \Up\Controller\BasketController(), 'addProductAction');
Router::delete('/deleteFromBasket/:id/', new \Up\Controller\BasketController(), 'deleteProductAction');
Router::get('/products-json/', new \Up\Controller\PageMainController(), 'getProductsJsonAction');
Router::get('/tags-json/', new \Up\Controller\PageMainController(), 'getTagsJsonAction');
Router::get('/search-json/', new \Up\Controller\PageMainController(), 'getSearchJsonAction');

/*Router::group([], [
	Router::post('/api/auth/login/', new \Up\Controller\AuthController(), 'loginAction'),
	Router::post('/api/auth/refresh-tokens/', new \Up\Controller\AuthController(), 'refreshTokensAction'),
	Router::post('/api/auth/logout/', new \Up\Controller\AuthController(), 'logoutAction')
	]);*/

Router::group(['preMiddleware' => 'isNotLogIn'], [
	Router::get('/admin/logIn/', new \Up\Controller\PageAdminController(), 'logInAction'),
	Router::post('/logging/', new \Up\Controller\AuthController(), 'authAction'),
	Router::post('/admin/signUp/', new \Up\Controller\AuthController(), 'logInAction')->redirect('/admin/'),
	]);


Router::group(['preMiddleware' => ['isLogin', 'isAdmin'], 'postMiddleware' => 'isAdminUnauthorized'], [
	Router::get('/admin/', new \Up\Controller\PageAdminController(), 'showProductsAction'),

	Router::patch('/admin/product/disable/', new \Up\Controller\ProductAdminController(), 'disableAction'),
	Router::patch('/admin/product/restore/', new \Up\Controller\ProductAdminController(), 'restoreAction'),
	Router::patch('/admin/product/change/', new \Up\Controller\ProductAdminController(), 'changeAction'),
	Router::post('/admin/product/image/', new \Up\Controller\ProductAdminController(), 'imageAction'),
	Router::post('/admin/product/add/', new \Up\Controller\ProductAdminController(), 'addAction'),

	Router::patch('/admin/user/disable/', new \Up\Controller\UserAdminController(), 'disableAction'),

	Router::delete('/admin/order/', new \Up\Controller\OrderAdminController(), 'deleteAction'),
	Router::post('/admin/order/add/', new \Up\Controller\OrderAdminController(), 'addAction'),
	Router::patch('/admin/order/change/', new \Up\Controller\OrderAdminController(), 'changeAction'),

	Router::delete('/admin/tag/', new \Up\Controller\TagAdminController(), 'deleteAction'),
	Router::post('/admin/tag/add/', new \Up\Controller\TagAdminController(), 'addAction'),
	Router::patch('/admin/tag/change/', new \Up\Controller\TagAdminController(), 'changeAction'),
	]);


Router::post('/product/:id/', new \Up\Controller\PageDetailController(), 'buyProductAction');

Router::get('/success/', new \Up\Controller\PageDetailController(), 'showModalSuccess');


Router::post('/account/logging/', new \Up\Controller\PageAccountController(), 'signUpAction');
Router::group(['preMiddleware' => ['isLogin']], [
	Router::get('/account/', new \Up\Controller\PageAccountController(), 'indexAction'),
	Router::patch('/account/edit/', new \Up\Controller\ChangeAccountController(), 'changeAction'),
	]);

Router::post('/createOrder/', new \Up\Controller\OrderController(), 'createOrder')->preMiddleware('isLogin');
