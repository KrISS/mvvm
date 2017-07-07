<?php

function responseException($app, $next = null) {
    if (!is_null($next)) {
        return function() use ($app, $next) {
            $container = $app->getContainer();
            try {
                return $next();
            } catch(\Exception $e) {
                if (!$container->has('ExceptionResponse')) {
                    $container->set('ExceptionResponse', [
                        'instanceOf' => 'Kriss\\Core\\Response\\ExceptionResponse',
                    ]);
                }             
                $container->set('ExceptionResponse', [
                    'constructParams' => [$e]
                ]);   
                
                return $container->get('ExceptionResponse');
            }
        };
    }
}

if (isset($app)) $app->addPlugin('responseException');
