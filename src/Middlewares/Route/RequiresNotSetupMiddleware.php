<?php

namespace Asko\Sember\Middlewares\Route;

use Asko\Sember\Database;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\User;
use Asko\Sember\Response;

readonly class RequiresNotSetupMiddleware
{
    public function __construct(private Database $db)
    {
    }

    public function handle(Response $response): ?Response
    {
        $user_exists = $this->db->findOne(User::class, 'where role = ?', ['admin']);
        $site_name_exists = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_name']);

        if ($user_exists && $site_name_exists) {
            return $response->redirect('/admin');
        }

        return null;
    }
}