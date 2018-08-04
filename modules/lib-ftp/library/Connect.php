<?php
/**
 * FTP Connector
 * @package lib-ftp
 * @version 0.0.2
 */

namespace LibFtp\Library;

class Connect
{
    private $handler;

    public function __construct(array $opts){
        $type = $opts['type'] ?? 'ftp';
        $handlers = \Mim::$app->config->libFtp->handlers;
        if(!isset($handlers->{$type}))
            trigger_error('Ftp type `' . $type . '` handler is not defined');

        $handler = $handlers->{$type};

        $this->handler = new $handler($opts);
    }

    public function __call($name, $args){
        return call_user_func_array([$this->handler, $name], $args);
    }
}