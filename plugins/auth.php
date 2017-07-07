<?php

use Kriss\Core\Response\RedirectResponse;

include_once('modelArray.php');

function auth($app, $next = null) {
    
    if (is_null($next)) {
        class Admin {
            public $username = 'admin';
            public $password = 'pass';
        }
        
        $container = $app->getContainer();
        
        $container->set('Session', [
            'instanceOf' => 'Kriss\\Core\\Session\\Session',
            'shared' => true,
            'constructParams' => [
                'KrISS',
            ]
        ]);
        modelArray($container, ['admin' => 'Admin']);

        // if routerAuto plugin available
        $container->set('#admin_form', [
            'instanceOf' => 'Kriss\\Core\\Form\\Form',
            'call' => [
                ['setRule', [
                    'username', []
                ]],
                ['setRule', [
                    'password', ['value' => '']
                ]],
                ['setFormatter', [
                    'password',
                    function ($value, $formData) use ($container) {
                        $hashPassword = $container->get('HashPassword');
                        return $hashPassword->hash($value, $formData['username']);
                    }
                ]]
            ]
        ]);

        $adminValidator = [
            'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
            'call' => [
                ['setConstraints', [
                    'password', [['minLength', [4], 'password requires at least 4 characters']],
                ]],
            ],
        ];
        $container->set('#admin_create_validator', $adminValidator);
        $container->set('#admin_update_validator', $adminValidator);
        
        $container->set('HashPassword', [
            'instanceOf' => 'Kriss\\Core\\Auth\\HashPassword',
        ]);
        $container->set('UserProvider', [
            'instanceOf' => 'Kriss\\Core\\Auth\\UserProvider',
            'shared' => true,
            'constructParams' => [
                ['instance' => '#admin_model'],
            ]
        ]);
        if (!$container->has('Authorization')) {
            $container->set('Authorization', [
                'instanceOf' => 'Kriss\\Core\\Auth\\ProtectedRequestAuthorization',
                'shared' => true,
                'constructParams' => [
                    ['instance' => 'Authentication'],
                    ['instance' => 'Request'],
                ]
            ]);
        }
    } else {
        return function() use ($app, $next) {
            $container = $app->getContainer();
            $container->get('Session')->start();
            
            $authentication = $container->get('Authentication');
            $authorization = $container->get('Authorization');

            $authentication->authenticate();
            $request = $container->get('Request');
            $adminUrl = '/admin/new/';
            $install = empty($container->get('#admin_model')->getData());
            if ($install && $request->getPathInfo() !== '/admin/' && $request->getPathInfo() !== $adminUrl) {
                return new RedirectResponse($request->getSchemeAndHttpHost().$request->getBaseUrl().$adminUrl);
            } else if ($authorization->isGranted() || $install) {
                return $next();
            } else {
                return $container->get('UnauthorizedResponse');
            }
        };
    }
}
