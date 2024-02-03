<?php

use Asko\Nth\Controllers\AdminController;
use Asko\Nth\Controllers\AuthenticationController;
use Asko\Nth\Controllers\SetupController;
use Asko\Nth\Controllers\SiteController;

return function(\Asko\Router\Router $router) {
    // Admin
    $router->get('/admin', [AdminController::class, 'index']);
    $router->get('/admin/signin', [AuthenticationController::class, 'signIn']);
    $router->post('/admin/signin', [AuthenticationController::class, 'signIn']);
    $router->get('/admin/signout', [AdminController::class, 'signOut']);
    $router->get('/admin/posts', [AdminController::class, 'posts']);
    $router->get('/admin/posts/new', [AdminController::class, 'createPost']);
    $router->get('/admin/posts/edit/{id}', [AdminController::class, 'editPost']);

    // Setup
    $router->get('/setup/account', [SetupController::class, 'account']);
    $router->post('/setup/account', [SetupController::class, 'account']);
    $router->get('/setup/site', [SetupController::class, 'site']);
    $router->post('/setup/site', [SetupController::class, 'site']);

    // Site
    $router->get('/', [SiteController::class, 'home']);
    $router->get('/{slug}', [SiteController::class, 'post']);
};