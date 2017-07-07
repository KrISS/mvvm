<?php

use Kriss\Core\Response\RedirectResponse;

include_once('auth.php');

function authBasic($app, $next = null) {
    if (is_null($next)) {
        $container = $app->getContainer();
        
        $routerRule = $container->getRule('Router');
        $container->set('Router', [
            'call' => array_merge([
                ['setRoute', [
                    'logout', 'GET', '/logout/',
                    function () use ($container) {
                        $request = $container->get('Request');
                        $authentication = $container->get('Authentication');
                        $authentication->deauthenticate();

                        return new RedirectResponse($request->getSchemeAndHttpHost().$request->getBaseUrl());
                    }
                ]],['setRoute', [
                    'login', 'GET', '/login/',
                    function () use ($container) {
                        $authentication = $container->get('Authentication');
                        $authentication->isAuthenticated();
                        if ($authentication->isAuthenticated()) {
                            $request = $container->get('Request');
                            return new RedirectResponse($request->getSchemeAndHttpHost().$request->getBaseUrl());
                        }
                        return $container->get('UnauthorizedResponse');
                    }
                ]]
            ], isset($routerRule['call'])?$routerRule['call']:[])
        ]);
        $container->set('Authentication', [
            'instanceOf' => 'Kriss\\Core\\Auth\\BasicAuthentication',
            'shared' => true,
            'constructParams' => [
                ['instance' => 'UserProvider'],
                ['instance' => 'Request'],
                ['instance' => 'Session'],
                ['instance' => 'HashPassword'],
            ]
        ]);
        $container->set('UnauthorizedResponse', [
            'instanceOf' => 'Kriss\\Core\\Response\\BasicUnauthorizedResponse',
            'constructParams' => [
                ['instance' => 'Session'],
                ['instance' => 'Request']
            ]
        ]);
    }

    return auth($app, $next);
}
