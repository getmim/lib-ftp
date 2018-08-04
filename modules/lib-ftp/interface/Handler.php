<?php
/**
 * @package lib-ftp
 * @version 0.0.2
 */

namespace LibFtp\Iface;

interface Handler
{
    public function close(): void;
    public function copy(string $source, string $target, string $type='text'): bool;
    public function exists(string $path): bool;
    public function getConn();
    public function getError(): ?string;
    public function isDir(string $path): bool;
    public function mkdir(string $path): bool;
    public function read(string $path, string $type='text', int $pos=0): ?string;
    public function rename(string $source, string $target): bool;
    public function rmdir(string $path): bool;
    public function scan(string $path): array;
    public function unlink(string $path): bool;
    public function write(string $path, $content, string $type='text', int $pos=0): bool;
}