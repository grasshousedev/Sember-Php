<?php

namespace Sember\System\Middlewares\Route;

use Sember\System\Database;
use Sember\System\Models\Meta;
use Sember\System\Models\User;
use Sember\System\Response;

readonly class RequiresSetupMiddleware
{
    public function __construct(private Database $db)
    {
    }

    public function handle(Response $response): ?Response
    {
        $user_exists = $this->db->findOne(User::class, 'where role = ?', ['admin']);
        $site_name_exists = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_name']);

        if (!$user_exists || !$site_name_exists) {
            return $response->redirect('/setup/account');
        }

        return null;
    }
}