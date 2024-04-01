<?php

namespace Sember\System\Controllers;

use Sember\System\Database;
use Sember\System\Models\User;
use Sember\System\Request;
use Sember\System\Response;
use Exception;
use Ramsey\Uuid\Uuid;

readonly class AuthenticationController
{
    public function __construct(private Database $db)
    {
    }

    /**
     * @throws Exception
     */
    public function signIn(Request $request, Response $response): Response
    {
        // Sign in
        if ($request->isPost()) {
            $user = $this->db->findOne(User::class, 'where email = ?', [$request->input('email')]);

            if ($user && password_verify($request->input('password'), $user->get('password'))) {
                $auth_token = Uuid::uuid4()->toString();
                $request->cookie()->set('auth_token', $auth_token);
                $user->set('auth_token', $auth_token);

                $this->db->update($user);

                return $response->redirect('/admin/posts');
            }

            return $response->redirect('/admin/signin')
                ->flash('email', $request->input('email'))
                ->flash('errors', ['Invalid email or password.']);
        }

        // Show form
        return $response->systemView('admin/signin', [
            'email' => $request->flash('email') ?? '',
            'errors' => $request->flash('errors') ?? [],
            'message' => $request->flash('message') ?? false,
        ]);
    }
}