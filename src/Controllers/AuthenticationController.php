<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\DB;
use Asko\Sember\Models\User;
use Asko\Sember\Request;
use Asko\Sember\Response;
use Exception;
use Ramsey\Uuid\Uuid;

class AuthenticationController extends Controller
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setupGuard();
    }

    /**
     * @throws Exception
     */
    public function signIn(Request $request, Response $response): Response
    {
        // Sign in
        if ($request->isPost()) {
            $find_user_query = [
                'email' => $request->input('email'),
                'password' => fn($hash) => password_verify($request->input('password'), $hash ?? '')
            ];

            if ($user = DB::find(User::class, $find_user_query)) {
                $auth_token = Uuid::uuid4()->toString();
                $request->session()->set('auth_token', $auth_token);
                $user->set('auth_token', $auth_token);

                DB::update($user);

                return $response->redirect('/admin/posts');
            }

            return $response->redirect('/admin/signin')
                ->flash('email', $request->input('email'))
                ->flash('errors', ['Invalid email or password.']);
        }

        // Show form
        return $response->view('admin/signin', [
            'email' => $request->flash('email') ?? '',
            'errors' => $request->flash('errors') ?? [],
            'message' => $request->flash('message') ?? false,
        ]);
    }
}