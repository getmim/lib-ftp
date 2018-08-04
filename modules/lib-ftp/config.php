<?php

return [
    '__name' => 'lib-ftp',
    '__version' => '0.0.2',
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
            'LibFtp\\Handler' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp/handler'
            ],
            'LibFtp\\Iface' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp/interface'
            ],
            'LibFtp\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp/library'
            ],
            'LibFtp\\Server' => [
                'type' => 'file',
                'base' => 'modules/lib-ftp/server'
            ]
        ],
        'files' => []
    ],
    'server' => [
        'lib-ftp' => [
            'PHP FTP Ext' => 'LibFtp\\Server\\PHP::ftp',
            'PHP OpenSSL Ext' => 'LibFtp\\Server\\PHP::openssl'
        ]
    ],

    'libFtp' => [
        'handlers' => [
            'ftp'  => 'LibFtp\\Handler\\Ftp',
            'ftps' => 'LibFtp\\Handler\\Ftp'
        ]
    ]
];