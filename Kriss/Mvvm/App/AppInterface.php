<?php

namespace Kriss\Mvvm\App;

interface AppInterface 
{
    public function addPlugin($name);
    public function configPlugin($name, $config);
    public function run();
}