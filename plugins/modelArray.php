<?php

function modelArray($container, $models = []) {  
    foreach($models as $slug => $class) {
        $id = '#'.$slug.'_model';
        if (!$container->has($id)) {
            $container->set($id, [
                'instanceOf' => 'Kriss\\Core\\Model\\ArrayModel',
                'shared' => true,
                'constructParams' => [
                    $slug,
                    $class
                ]
            ]);
        }
    }             
}
