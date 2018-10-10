<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('brands', BrandsController::class);
    $router->resource('series', SeriesController::class);
    $router->resource('cmodels', CmodelsController::class);
    $router->resource('colors', ColorsController::class);
    $router->resource('agents', AgentsController::class);
    $router->resource('franchisee/levels', FranchiseeLevelsController::class);
    $router->resource('franchisees', FranchiseesController::class);
    $router->resource('workers', WorkersController::class);
    $router->resource('stypes', StypesController::class);
    $router->resource('banners', BannersController::class);
    $router->resource('applies', AppliesController::class);
    $router->resource('users', UsersController::class);
    $router->resource('orders', OrdersController::class);
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    $router->post('orders/{order}/confirm', 'OrdersController@confirm')->name('admin.orders.confirm');
    $router->post('orders/{order}/refund', 'OrdersController@refund')->name('admin.orders.refund');

    $router->get('/api/series', 'ApisController@series');
    $router->post('/api/check', 'ApisController@check')->name('admin.api.check');

});
