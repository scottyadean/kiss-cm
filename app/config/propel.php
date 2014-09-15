<?php

return [
    'propel' => [
        'database' => [
            'connections' => [
                'mysource' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\DebugPDO',
                    'dsn'        => 'mysql:host=localhost;dbname=mydb',
                    'user'       => 'root',
                    'password'   => '',
                    'attributes' => []
                ],
                'yoursource' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\DebugPDO',
                    'dsn'        => 'mysql:host=localhost;dbname=yourdb',
                    'user'       => 'root',
                    'password'   => '',
                    'attributes' => []
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'mysource',
            'connections' => ['mysource', 'yoursource']
        ],
        'generator' => [
            'defaultConnection' => 'yoursource',
            'connections' => ['yoursource']
        ]
    ]          
];