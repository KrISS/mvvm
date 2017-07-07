<?php

//include('../mvvm/plugins/modelMaphper.php');
//modelMaphper($app, [['bang' => 'Bang']]);

function modelMaphper($container, $models = []) {
    $container->set('PDO', [
        'constructParams' => ['sqlite:data/data.db'],
        'call' => [
            ['setAttribute', [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]]
        ]
    ]);
    foreach($models as $slug => $class) {
        $container->set('#'.$slug.'_datasource', [
            'instanceOf' => 'Maphper\\DataSource\\Database', 
            'constructParams' => [
                ['instance' => 'PDO'],
                $slug,
                'id',
                ['editmode' => true],
            ]
        ]);
        
        $container->set('#'.$slug.'_maphper', [
            'instanceOf' => 'Maphper\\Maphper',
            'constructParams' => [
                ['instance' => '#'.$slug.'_datasource'],
                ['resultClass' => $class],
            ]
        ]);
        
        $container->set('#'.$slug.'_model', [
            'instanceOf' => 'Kriss\\Core\\Model\\MaphperModel',
            'constructParams' => [
                ['instance' => '#'.$slug.'_maphper'],
                $slug,
                $class,
            ]
        ]);
    }
}
