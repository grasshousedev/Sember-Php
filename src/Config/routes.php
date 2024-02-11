<?php

use Asko\Sember\Controllers\AdminAPIController;
use Asko\Sember\Controllers\AdminController;
use Asko\Sember\Controllers\AuthenticationController;
use Asko\Sember\Controllers\SetupController;
use Asko\Sember\Controllers\SiteController;

return function(\Asko\Router\Router $router) {
    // Admin
    $router->get('/admin', [AdminController::class, 'index']);
    $router->get('/admin/signin', [AuthenticationController::class, 'signIn']);
    $router->post('/admin/signin', [AuthenticationController::class, 'signIn']);
    $router->get('/admin/signout', [AdminController::class, 'signOut']);
    $router->get('/admin/posts', [AdminController::class, 'posts']);
    $router->get('/admin/posts/new', [AdminController::class, 'createPost']);
    $router->get('/admin/posts/edit/{id}', [AdminController::class, 'editPost']);
    $router->get('/admin/posts/delete/{id}', [AdminController::class, 'deletePost']);

    // Admin API
    $router->get('/admin/api/post/{id}/editor', [AdminAPIController::class, 'editor']);
    $router->get('/admin/api/post/{id}/status', [AdminAPIController::class, 'status']);
    $router->get('/admin/api/post/{id}/published-at', [AdminAPIController::class, 'publishedAt']);
    $router->post('/admin/api/post/{id}/update-title', [AdminAPIController::class, 'updateTitle']);
    $router->post('/admin/api/post/{id}/update-slug', [AdminAPIController::class, 'updateSlug']);
    $router->post('/admin/api/post/{id}/update-status', [AdminAPIController::class, 'updateStatus']);
    $router->post('/admin/api/post/{id}/update-published-at', [AdminAPIController::class, 'updatePublishedAt']);
    $router->post('/admin/api/post/{id}/blocks/add/{type}/{position}', [AdminAPIController::class, 'addBlock']);
    $router->post('/admin/api/post/{id}/blocks/{blockId}', [AdminAPIController::class, 'updateBlock']);
    $router->delete('/admin/api/post/{id}/blocks/{blockId}', [AdminAPIController::class, 'deleteBlock']);
    $router->post('/admin/api/post/{id}/blocks/{blockId}/move/{direction}', [AdminAPIController::class, 'moveBlock']);
    $router->post('/admin/api/post/{id}/blocks/{blockId}/opt/{fn}/{arg}', [AdminAPIController::class, 'blockOption']);

    // Setup
    $router->get('/setup/account', [SetupController::class, 'account']);
    $router->post('/setup/account', [SetupController::class, 'account']);
    $router->get('/setup/site', [SetupController::class, 'site']);
    $router->post('/setup/site', [SetupController::class, 'site']);

    // Site
    $router->get('/', [SiteController::class, 'home']);
    $router->get('/{slug}', [SiteController::class, 'post']);

    // Not found
    $router->notFound([SiteController::class, 'notFound']);
};