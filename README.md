# lib-ftp

Adalah library yang menangani koneksi ftp. Library ini membutuhkan
ekstensi `php-ftp` terpasang pada server.

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-ftp
```

## Penggunaan

Buatkan object `LibFtp\Library\Ftp` dengan parameter koneksi, sebagai berikuta:

```php
$opts = [
    'server' => [
        'host'      => 'ftp.host.ext',
        'port'      => 21,
        'timeout'   => 90,
        'ssl'       => false
    ],
    'user' => [
        'name'      => 'user',
        'password'  => '/secret/'
    ],
    'base' => '/home/iqbal'
];
$ftp = new LibFtp\Library\Ftp($opts);
if($ftp->getError())
    deb($ftp->getError());
```

kemudian koneksi ftp siap digunakan.

## methods

### close(): void

### copy(string $source, string $target, string $type='text'): bool

Nilai property `$type` yang diterima adalah `text` dan `binary`.

### exists(string $path): bool

### getError(): ?string

### getConn(): ?object

### mkdir(string $path): bool

### isDir(string $path): bool

### read(string $path, string $type='text', int $post): ?string

Nilai property `$type` yang diterima adalah `text` dan `binary`.

### rename(string $source, string $target): bool

### rmdir(string $path): bool

### scan(string $path): ?array

### unlink(string $path): bool

### write(string $path, $text, string $type='text', int $pos=0): bool

Nilai property `$type` yang diterima adalah `text` dan `binary`.