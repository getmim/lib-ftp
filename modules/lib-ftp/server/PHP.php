<?php
/**
 * lib-ftp server tester
 * @package lib-ftp
 * @version 0.0.1
 */

namespace LibFtp\Server;

class PHP
{
    static function ftp(){
        return [
            'success' => function_exists('ftp_connect'),
            'info' => ''
        ];
    }

    static function openssl(){
        return [
            'success' => extension_loaded('openssl'),
            'info' => ''
        ];
    }
}