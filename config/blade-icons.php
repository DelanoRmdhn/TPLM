<?php

/*
 * Ikon dipanggil lewat komponen kita sendiri <x-icon name="..."> (resources/views/components/icon.blade.php)
 * yang memakai helper svg(). Komponen bawaan blade-icons dimatikan: selain bentrok dengan <x-icon>,
 * mendaftarkan ±9.000 komponen ikon Phosphor juga memperlambat aplikasi.
 */
return [
    'components' => [
        'disabled' => true,
        'default' => null,
    ],
];
