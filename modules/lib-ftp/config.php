<?php

return [
    '__name' => 'lib-ftp',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/lib-ftp.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/lib-ftp' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'LibFtp\\Server' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp/server'
            ],
            'LibFtp\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp/library'
            ]
        ],
        'files' => []
    ],
    'server' => [
        'lib-ftp' => [
            'PHP FTP Ext' => 'LibFtp\\Server\\PHP::ftp'
        ]
    ]
];