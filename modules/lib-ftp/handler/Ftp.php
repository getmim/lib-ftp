<?php
/**
 * Ftp
 * @package lib-ftp
 * @version 0.0.2
 */

namespace LibFtp\Handler;

use \Mim\Library\Fs;

class Ftp implements \LibFtp\Iface\Handler
{

    private $error;
    private $conn;
    private $base;

    public function _silent(){}

    public function __construct(array $opts){
        $server = $opts['server'];
        if(!isset($server['port']))
            $server['port'] = 21;
        if(!isset($server['timeout']))
            $server['timeout'] = 90;

        $ssl = $opts['type'] ?? 'ftp';
        $func = $ssl === 'ftps' ? 'ftp_ssl_connect' : 'ftp_connect';

        set_error_handler([$this, 'setError']);
        $this->conn = call_user_func_array($func, [
            $server['host'],
            $server['port'],
            $server['timeout']
        ]);

        if(!$this->conn){
            restore_error_handler();
            return;
        }
        restore_error_handler();

        if(!isset($opts['user']))
            return;

        $user = $opts['user'];

        set_error_handler([$this, 'setError']);
        if(!ftp_login($this->conn, $user['name'], $user['password'])){
            restore_error_handler();
            return;
        }
        restore_error_handler();

        ftp_pasv($this->conn, true);

        $this->base = ftp_pwd($this->conn);

        if(isset($opts['base'])){
            set_error_handler(function(){});
            if(!@ftp_chdir($this->conn, $opts['base']))
                $this->error = 'Unable to set active directory to ' . $opts['base'];
            restore_error_handler();
        }
    }

    public function close(): void{
        if(!$this->conn)
            return;
        ftp_close($this->conn);
        $this->conn = null;
    }

    public function copy(string $source, string $target, string $type='text'): bool {
        $mode = $type === 'text' ? FTP_ASCII : FTP_BINARY;
        $tmp = tempnam(sys_get_temp_dir(), 'mim-ftp-');

        if(!ftp_get($this->conn, $tmp, $source, $mode, 0))
            return false;
        if(!ftp_put($this->conn, $target, $tmp, $mode, 0))
            return false;
        return true;
    }

    public function download(string $source, string $target, string $type='text', int $pos=0): bool{
        $mode = $type === 'text' ? FTP_ASCII : FTP_BINARY;
        $result = ftp_get($this->conn, $target, $source, $type, $pos);
        return $result;
    }

    public function exists(string $path): bool {
        $parent = dirname($path);
        $fname  = basename($path);
        $files = $this->scan($parent);
        return in_array($fname, $files);
    }

    public function getConn(){
        return $this->conn;
    }

    public function getError(): ?string {
        return $this->error;
    }

    public function isDir(string $path): bool{
        $parent = dirname($path);
        $fname  = basename($path);
        $files  = ftp_mlsd($this->conn, $parent);

        foreach($files as $file){
            if($file['name'] === $fname){
                if($file['type'] === 'dir')
                    return true;
                return false;
            }
        }

        return false;
    }

    public function mkdir(string $path): bool {
        ftp_chdir($this->conn, $this->base);

        $paths = explode('/', trim($path, '/'));
        $cpath = '/';
        $result = true;

        set_error_handler([$this, '_silent']);
        foreach($paths as $path){
            $cpath.= $path . '/';
            if(!ftp_chdir($this->conn, $cpath)){
                if(!ftp_mkdir($this->conn, $cpath)){
                    $result = false;
                    break;
                }
            }

            if(!ftp_chdir($this->conn, $cpath)){
                $result = false;
                break;
            }
        }
        restore_error_handler();

        return $result;
    }

    public function read(string $path, string $type='text', int $pos=0): ?string {
        $tmp = tempnam(sys_get_temp_dir(), 'mim-ftp-');
        $mode = $type === 'text' ? FTP_ASCII : FTP_BINARY;
        $result = ftp_get($this->conn, $tmp, $path, $mode, $pos);
        if(false === $result)
            return null;
        $result = file_get_contents($tmp);
        unlink($tmp);
        return $result;
    }

    public function rename(string $source, string $target): bool {
        return @ftp_rename($this->conn, $source, $target);
    }

    public function rmdir(string $path): bool {
        $path = chop($path, '/');
        $files = ftp_mlsd($this->conn, $path);

        foreach($files as $file){
            $file_abs = $path . '/' . $file['name'];

            if($file['name'] === '.' || $file['name'] === '..')
                continue;

            if($file['type'] != 'dir'){
                if(!ftp_delete($this->conn, $file_abs))
                    return false;
            }else{
                if(!$this->rmdir($path . '/' . $file['name']))
                    return false;
            }
        }

        return ftp_rmdir($this->conn, $path);
    }

    public function scan(string $path): array {
        $files = ftp_mlsd($this->conn, $path);
        if(!$files)
            return [];
        $files = array_column($files, 'name');
        return array_values(array_diff($files, ['.','..']));
    }

    public function setError($no, $text, $file, $line): void{
        $this->error = $text;
    }

    public function unlink(string $path): bool{
        return ftp_delete($this->conn, $path);
    }

    public function upload(string $path, string $source, string $type='text', int $pos=0): bool{
        $parent = dirname($path);
        if(!$this->mkdir($parent))
            return false;

        $mode = $type === 'text' ? FTP_ASCII : FTP_BINARY;
        $result = ftp_put($this->conn, $path, $source, $mode, $pos);
        return $result;
    }

    public function write(string $path, $content, string $type='text', int $pos=0): bool {
        $parent = dirname($path);
        if(!$this->mkdir($parent))
            return false;

        $tmp = tempnam(sys_get_temp_dir(), 'mim-ftp-');
        Fs::write($tmp, $content);

        $mode = $type === 'text' ? FTP_ASCII : FTP_BINARY;

        set_error_handler([$this, 'setError']);
        $result = ftp_put($this->conn, $path, $tmp, $mode, $pos);
        restore_error_handler();

        unlink($tmp);
        return $result;
    }
}