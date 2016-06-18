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
    public $username = 'username';

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return sha1('usernametoto');
    }
}

$container = new DiceContainer(new Dice\Dice);

$app = new RestApp(
    $container
    , ['config' => 'Config', 'admin' => 'User']
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

$container->set('$users_model', [
    'instanceOf' => 'Kriss\\Core\\Model\\MaphperModel',
    'constructParams' => [
        'users',
        'User',
        ['instance' => 'User\\Maphper'],
    ]
]);

$container->set('*user_validator', [
    'instanceOf' => 'Kriss\\Core\\Validator\\HybridLogicValidator',
    'call' => [
        ['setConstraints', [[
            ['email', 'email', [], 'Email error'],
        ]]],
    ],
]);

$container->set('$config_update_validator', [
    'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
    'call' => [
        ['setConstraints', [[
            ['test', 'email', [], 'Test email error'],
        ]]],
    ],
]);

$container->set('*config_create_view_model', [
    'instanceOf' => 'Kriss\\Core\\ViewModel\\RedirectFormViewModel',
]);

$container->set('$configs_index_id_view', [
    'instanceOf' => 'Kriss\\Core\\View\\JsonView',
    'constructParams' => [
        ['instance' => '$configs_index_id_view_model'],
    ]
]);

$container->set('Transphporm\\Builder', [
    'constructParams' => [
        'Kriss/Rest/View/Transphporm/template.xml',
        'Kriss/Rest/View/Transphporm/template.tss',
    ]    
]);

$container->set('$users_index_view', [
    'instanceOf' => 'Kriss\\Rest\\View\\TransphpormView',
    'constructParams' => [
        ['instance' => '$users_index_view_model'],
        ['instance' => 'Transphporm\\Builder']
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

$router->addResponse('index_hello_tutu', 'GET', '/hi/<name:\d>', function ($name = "world") {
    return new Response('Hello '.$name);
});
$router->addResponse('index_test', 'GET', '/test', new Response('Hello Test'));

$router->addResponse('index_test_route', 'GET', '/test_route', function () use ($router) {
        return new Response($router->generate('autoroute_edit_id', ['slug' => 'users', 'id' => 1]));
});

$app->run();
