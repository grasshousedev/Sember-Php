<?php

use Asko\Sember\Controllers\AdminAPIController;
use Asko\Sember\Controllers\AdminController;
use Asko\Sember\Controllers\AuthenticationController;
use Asko\Sember\Controllers\SetupController;
use Asko\Sember\Controllers\SiteController;
use Asko\Sember\Middlewares\Route\IsAuthenticatedMiddleware;
use Asko\Sember\Middlewares\Route\RequiresNotSetupMiddleware;
use Asko\Sember\Middlewares\Route\RequiresSetupMiddleware;

return function(\Asko\Router\Router $router) {
    // -------------------------------------
    // Routes
    // -------------------------------------
    $router
        ->get('/admin', [AdminController::class, 'index'])
        ->middleware(RequiresSetupMiddleware::class);

    $router
        ->get('/admin/signin', [AuthenticationController::class, 'signIn'])
        ->middleware(RequiresSetupMiddleware::class);

    $router
        ->post('/admin/signin', [AuthenticationController::class, 'signIn'])
        ->middleware(RequiresSetupMiddleware::class);

    $router
        ->get('/admin/signout', [AdminController::class, 'signOut'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->get('/admin/posts', [AdminController::class, 'posts'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->get('/admin/posts/new', [AdminController::class, 'createPost'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->get('/admin/posts/edit/{id}', [AdminController::class, 'editPost'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->get('/admin/posts/delete/{id}', [AdminController::class, 'deletePost'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    // -------------------------------------
    // Admin API
    // -------------------------------------
    $router
        ->get('/admin/api/post/{id}/editor', [AdminAPIController::class, 'editor'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->get('/admin/api/post/{id}/status', [AdminAPIController::class, 'status'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->get('/admin/api/post/{id}/published-at', [AdminAPIController::class, 'publishedAt'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/update-title', [AdminAPIController::class, 'updateTitle'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/update-slug', [AdminAPIController::class, 'updateSlug'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/update-status', [AdminAPIController::class, 'updateStatus'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/update-published-at', [AdminAPIController::class, 'updatePublishedAt'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/blocks/add/{type}/{position}', [AdminAPIController::class, 'addBlock'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/blocks/{blockId}', [AdminAPIController::class, 'updateBlock'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->delete('/admin/api/post/{id}/blocks/{blockId}', [AdminAPIController::class, 'deleteBlock'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/blocks/{blockId}/move/{direction}', [AdminAPIController::class, 'moveBlock'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    $router
        ->post('/admin/api/post/{id}/blocks/{blockId}/opt/{fn}/{arg}', [AdminAPIController::class, 'blockOption'])
        ->middleware([RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]);

    // -------------------------------------
    // Setup
    // -------------------------------------
    $router
        ->get('/setup/account', [SetupController::class, 'account'])
        ->middleware(RequiresNotSetupMiddleware::class);

    $router
        ->post('/setup/account', [SetupController::class, 'account'])
        ->middleware(RequiresNotSetupMiddleware::class);

    $router
        ->get('/setup/site', [SetupController::class, 'site'])
        ->middleware(RequiresNotSetupMiddleware::class);

    $router
        ->post('/setup/site', [SetupController::class, 'site'])
        ->middleware(RequiresNotSetupMiddleware::class);

    // -------------------------------------
    // Site
    // -------------------------------------
    $router
        ->get('/', [SiteController::class, 'home'])
        ->middleware(RequiresSetupMiddleware::class);

    $router
        ->get('/{slug}', [SiteController::class, 'post'])
        ->middleware(RequiresSetupMiddleware::class);

    $router->notFound([SiteController::class, 'notFound']);
};