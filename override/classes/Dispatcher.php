<?php

class Dispatcher extends DispatcherCore
{
    public function __construct()
    {
        parent::__construct();

        $this->default_routes['module-puzzlecustomizer-customizer'] = [
            'controller' => 'customizer',
            'rule' => 'puzzle/personalizza',
            'keywords' => [],
            'params' => [
                'fc' => 'module',
                'module' => 'puzzlecustomizer',
            ],
        ];
    }
}
