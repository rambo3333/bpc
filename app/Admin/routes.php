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

    $router->get('/api/series', 'ApisController@series');

});
