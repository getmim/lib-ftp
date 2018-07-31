<?php
/**
 * Ftp
 * @package lib-ftp
 * @version 0.0.1
 */

namespace LibFtp\Library;

use \Mim\Library\Fs;

class Ftp
{

    private $error;
    private $conn;
    private $base;

    public function __construct(array $opts){
        $server = $opts['server'];
        if(!isset($server['port']))
            $server['port'] = 21;
        if(!isset($server['timeout']))
            $server['timeout'] = 90;

        $ssl = $server['ssl'] ?? null;
        $func = $ssl ? 'ftp_ssl_connect' : 'ftp_connect';

        $this->conn = call_user_func_array($func, [
            $server['host'],
            $server['port'],
            $server['timeout']
        ]);

        if(!$this->conn){
            $this->error = 'Unable to connect to ftp server';
            return;
        }

        if(!isset($opts['user']))
            return;

        $user = $opts['user'];

        if(!@ftp_login($this->conn, $user['name'], $user['password'])){
            $this->error = 'Unable to login to the ftp server';
            return;
        }

        ftp_pasv($this->conn, true);

        $this->base = ftp_pwd($this->conn);

        if(isset($opts['base'])){
            if(!@ftp_chdir($this->conn, $opts['base']))
                $this->error = 'Unable to set active directory to ' . $opts['base'];
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
        foreach($paths as $path){
            $cpath.= $path . '/';
            if(!@ftp_chdir($this->conn, $cpath)){
                if(!@ftp_mkdir($this->conn, $cpath))
                    return false;
            }

            if(!@ftp_chdir($this->conn, $cpath))
                return false;
        }

        return true;
    }

    public function read(string $path, string $type='text', int $pos=0): ?string {
        $tmp = tempnam(sys_get_temp_dir(), 'mim-ftp-');
        $mode = $type === 'text' ? FTP_ASCII : FTP_BINARY;
        $result = ftp_get($this->conn, $tmp, $path, $mode, $pos);
        if(false === $result)
            return null;
        return file_get_contents($tmp);
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
        $files = ftp_nlist($this->conn, $path);
        if(!$files)
            return [];
        return array_values(array_diff($files, ['.','..']));
    }

    public function unlink(string $path): bool{
        return ftp_delete($this->conn, $path);
    }

    public function write(string $path, $content, string $type='text', int $pos=0): bool {
        $parent = dirname($path);
        if(!$this->mkdir($parent))
            return false;

        $tmp = tempnam(sys_get_temp_dir(), 'mim-ftp-');
        Fs::write($tmp, $content);

        $mode = $type === 'text' ? FTP_ASCII : FTP_BINARY;
        $result = ftp_put($this->conn, $path, $tmp, $mode, $pos);
        unlink($tmp);
        return $result;
    }
}