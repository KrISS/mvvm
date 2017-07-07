<?php

use Kriss\Core\Response\RedirectResponse;
use Kriss\Core\Response\Response;

include_once('auth.php');

function authSession($app, $next = null) {
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
                    'login', ['GET', 'POST'], '/login/',
                    function () use ($container) {
                        $authentication = $container->get('Authentication');
                        $request = $container->get('Request');
                        if ($authentication->isAuthenticated()) {
                            $redirect = $request->getQuery('redirect', '');
                            $query = $request->getQuery();
                            unset($query['redirect']);
                            $query = http_build_query($query);
                            
                            return new RedirectResponse($request->getSchemeAndHttpHost().$request->getBaseUrl().$redirect.(empty($query)?'':'?'.$query));
                        }

                        return new Response((($request->getMethod() === 'POST')?'Wrong login':'').'<form method="POST">username:<input name="username" type="text"><br>password:<input name="password" type="password"><br><input type="submit"></form>');
                    }
                ]]
            ], isset($routerRule['call'])?$routerRule['call']:[])
        ]);
        $container->set('Authentication', [
            'instanceOf' => 'Kriss\\Core\\Auth\\SessionAuthentication',
            'shared' => true,
            'constructParams' => [
                ['instance' => 'UserProvider'],
                ['instance' => 'Request'],
                ['instance' => 'Session'],
                ['instance' => 'HashPassword'],
            ]
        ]);
        $container->set('UnauthorizedResponse', [
            'instanceOf' => 'Kriss\\Core\\Response\\RedirectResponse',
            'constructParams' => [function() use ($container) {
                    $request = $container->get('Request');
                    $router = $container->get('Router');
                    $params = empty($request->getPathInfo())?$request->getQuery():array_merge(['redirect' => $request->getPathInfo()], $request->getQuery());
                    return $router->generate('login', $params);
                }]
        ]);
    }
        
    return auth($app, $next);
}
