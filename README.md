# lib-ftp

Adalah library yang menangani koneksi ftp. Library ini membutuhkan
ekstensi [php-ftp](http://php.net/manual/en/book.ftp.php) terpasang
pada server. Jika ingin menggunakan koneksi ssl ( ftps ) dengan 
konfigurasi `ssl => true` pastikan juga memasang ekstensi
[openssl](http://php.net/manual/en/book.openssl.php).

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-ftp
```

## Penggunaan

Buatkan object `LibFtp\Library\Connect` dengan parameter koneksi, sebagai berikuta:

```php
$opts = [
    'type' => 'ftp', // `ftps` untuk ssl
    'server' => [
        'host'      => 'ftp.host.ext',
        'port'      => 21,
        'timeout'   => 90
    ],
    'user' => [
        'name'      => 'user',
        'password'  => '/secret/'
    ]
];
$ftp = new LibFtp\Library\Connect($opts);
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

### read(string $path, string $type='text', int $pos=0): ?string

Nilai property `$type` yang diterima adalah `text` dan `binary`.

### rename(string $source, string $target): bool

### rmdir(string $path): bool

### scan(string $path): ?array

### unlink(string $path): bool

### write(string $path, $text, string $type='text', int $pos=0): bool

Nilai property `$type` yang diterima adalah `text` dan `binary`.

## Custom Handler

Sangat memungkinkan mengunakan custom handler. Jika ingin menggunakan
custom handler, maka pastikan mendaftarkan handler di konfigurasi
module dengan cara seperti di bawah:

```php
return [
    'libFtp' => [
        'handlers' => [
            'custom-handler' => 'Class'
        ]
    ]
];
```

Masing-masing handlers harus mengimplementasikan interface `LibFtp\Iface\Handler`.