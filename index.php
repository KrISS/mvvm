<?php

use Kriss\Rest\App\RestApp;
use Kriss\Core\Container\DiceContainer;
use Kriss\Core\Container\Container;
use Kriss\Core\Response\Response;

include('autoload.php');

class Config {
    public $conf = 'default_conf';
    public $test = 'default_test';
}

class User {
    public $firstname = 'default_firstname';
    public $lastname = 'default_lastname';
    public $email = 'default@email.net';
}

$container = new DiceContainer(new Dice\Dice);

$app = new RestApp(
    $container
    , ['config' => 'Config']
    , ['configs' => 'Config', 'users' => 'User']
);

$container->set('PDO', [
    'constructParams' => ['sqlite:data/data.db'],
    'call' => [
        ['setAttribute', [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]]
    ]
]);

$container->set('Maphper\\DataSource', [
    'instanceOf' => 'Maphper\\DataSource\\Database', 
    'constructParams' => [
        ['instance' => 'PDO'],
        'user',
        'id',
        ['editmode' => true],
    ]
]);

$container->set('User\\Maphper', [
    'instanceOf' => 'Maphper\\Maphper',
    'constructParams' => [
        ['instance' => 'Maphper\\DataSource'],
        ['resultClass' => '\User'],
    ]
]);

$container->set('User\\Model', [
    'instanceOf' => 'Kriss\\Core\\Model\\MaphperModel',
    'constructParams' => [
        'users',
        'User',
        ['instance' => 'User\\Maphper'],
    ]
]);

$container->set('User\\Validator', [
    'instanceOf' => 'Kriss\\Core\\Validator\\HybridLogicValidator',
    'call' => [
        ['setConstraints', [[
            ['email', 'email', [], 'Email error'],
        ]]],
    ],
]);

$container->set('Config\\Update\\Validator', [
    'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
    'call' => [
        ['setConstraints', [[
            ['test', 'email', [], 'Test email error'],
        ]]],
    ],
]);

$container->set('Config\\Create\\ViewModel', [
    'instanceOf' => 'Kriss\\Core\\ViewModel\\RedirectFormViewModel',
    'shared' => true,
    'constructParams' => [
        ['instance' => 'Config\\Model'],
        ['instance' => 'Config\\Validator'],
        ['instance' => 'Config'],
        'PUT',
    ]
]);

$container->set('Config\\IndexId\\View', [
    'instanceOf' => 'Kriss\\Core\\View\\JsonView',
    'constructParams' => [
        ['instance' => 'Config\\ViewModel'],
    ]
]);

$container->set('Transphporm\\Builder', [
    'constructParams' => [
        'Kriss/Rest/View/Transphporm/template.xml',
        'Kriss/Rest/View/Transphporm/template.tss',
    ]    
]);

$container->set('User\\Index\\View', [
    'instanceOf' => 'Kriss\\Rest\\View\\TransphpormView',
    'constructParams' => [
        ['instance' => 'User\\ViewModel'],
        ['instance' => 'Transphporm\\Builder'],
    ]
]);

$router = $container->get('Router');

$container->set('$route_GET', [
    'instanceOf' => 'Kriss\\Core\\Response\\Response',
    'constructParams' => [ 'Hello World' ],
]);

$router->addResponse('index', 'GET', '/get', function () use ($container) {
        return $container->get('$route_GET');
});

$router->addResponse('index_hello', 'GET', '/hello/<!name>', function ($name = "world") {
    return new Response('Hello '.$name);
});

$router->addResponse('index_test', 'GET', '/test', new Response('Hello Test'));

$router->addResponse('index_test_route', 'GET', '/test_route', function () use ($router) {
        return new Response($router->generate('autoroute_edit_id', ['class' => 'users', 'id' => 1]));
});

$app->run();
