<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\DB;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\User;
use Asko\Sember\Request;
use Asko\Sember\Response;
use Asko\Sember\Validator;
use Exception;

/**
 * @package Asko\Nth\Controllers
 * @since 0.1.0
 */
class SetupController
{
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
        if (DB::find(User::class, ['role' => 'admin'])) {
            return $response->redirect('/setup/site');
        }

        // Create user
        if ($request->isPost()) {
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

            DB::create(new User([
                'email' => $request->input('email'),
                'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
                'role' => 'admin'
            ]));

            return $response->redirect('/setup/site');
        }

        // Show form
        return $response->view('setup/account', [
            'email' => $request->flash('email'),
            'errors' => $request->flash('errors')
        ]);
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
        if (DB::find(Meta::class, ['meta_name' => 'site_config'])) {
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

            DB::create(new Meta([
                'meta_name' => 'site_config',
                'site_name' => $request->input('site_name'),
                'site_description' => $request->input('site_description', ''),
            ]));

            return $response->redirect('/admin/signin')
                ->flash('message', 'Congrats. Site has been set up, you can now sign in.');
        }

        // Show form
        return $response->view('setup/site', [
            'errors' => $request->flash('errors')
        ]);
    }
}