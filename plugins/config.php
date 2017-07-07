<?php

include_once('authSession.php');
include_once('authBasic.php');
include_once('modelArray.php');
include_once('routerAuto.php');
include_once('responseException.php');

function config($app) {
    $container = $app->getContainer();
    class Config {
        public $auth = 'authSession';
        public $visibility = 'protected';
    }
    modelArray($container, ['config' => 'Config']);
    $model = $container->get('#config_model');
    $config = $model->findOneBy();
    if (is_null($config)) $config = $container->get('Config');

    if (!empty($config->auth)) {
        $app->addPlugin($config->auth);
    }
    
    $configValidator = [
        'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
        'call' => [
            ['setConstraints', [
                'visibility', [['inArray', [['protected', 'private']], '"protected" or "private"']],
            ]],
            ['setConstraints', [
                'auth', [['inArray', [['', 'authSession', 'authBasic']], '"authSession", "authBasic" or empty: ""']],
            ]],
        ],
    ];
    $container->set('#config_create_validator', $configValidator);
    $container->set('#config_update_validator', $configValidator);
    $container->set('Authorization', [
        'instanceOf' => 'Kriss\\Core\\Auth\\'.ucfirst($config->visibility).'RequestAuthorization',
        'shared' => true,
        'constructParams' => [
            ['instance' => 'Authentication'],
            ['instance' => 'Request'],
        ]
    ]);
}
