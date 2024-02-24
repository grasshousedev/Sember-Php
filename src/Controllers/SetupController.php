<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\Database;
use Asko\Sember\Logger;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\User;
use Asko\Sember\Request;
use Asko\Sember\Response;
use Asko\Sember\Validator;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * @package Asko\Nth\Controllers
 * @since 0.1.0
 */
readonly class SetupController
{
    public function __construct(private Database $db)
    {
    }

    /**
     * Account set-up.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function account(Request $request, Response $response): Response
    {
        // If there is already an admin user, redirect to site set-up.
        if ($this->db->findOne(User::class, 'where role = ?', ['admin'])) {
           return $response->redirect('/setup/site');
        }

        // Show form
        return $response->view('setup/account', [
            'email' => $request->flash('email'),
            'errors' => $request->flash('errors'),
            'csrf_token' => $request->session()->set('csrf_token', Uuid::uuid4()->toString()),
        ]);
    }

    public function createAccount(Request $request, Response $response): Response
    {
        $validator = new Validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $response->redirect('/setup/account')
                ->flash('errors', $validator->errors())
                ->flash('email', $request->input('email'));
        }

        $this->db->create(new User([
            'email' => $request->input('email'),
            'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => time(),
            'updated_at' => time(),
        ]));

        return $response->redirect('/setup/site');
    }

    /**
     * Site set-up.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function site(Request $request, Response $response): Response
    {
        // If there already is site meta, redirect to signin
        if ($this->db->findOne(Meta::class, 'where meta_name = ?', ['site_name'])) {
            return $response->redirect('/admin/signin');
        }

        // Create site meta
        if ($request->isPost()) {
            $validator = new Validator($request->all(), [
                'site_name' => 'required',
            ]);

            if ($validator->fails()) {
                return $response->redirect('/setup/site')
                    ->flash('errors', $validator->errors());
            }

            $this->db->create(new Meta([
                'meta_name' => 'site_name',
                'meta_value' => $request->input('site_name'),
                'created_at' => time(),
                'updated_at' => time(),
            ]));

            if ($description = $request->input('site_description')) {
                $this->db->create(new Meta([
                    'meta_name' => 'site_description',
                    'meta_value' => $description,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]));
            }

            return $response->redirect('/admin/signin')
                ->flash('message', 'Congrats. Site has been set up, you can now sign in.');
        }

        // Show form
        return $response->view('setup/site', [
            'errors' => $request->flash('errors')
        ]);
    }
}