<?php

namespace App\Support;

/**
 * Kata sandi awal/sementara yang mudah dibaca anak SD:
 * 8 karakter huruf kecil + angka, tanpa huruf/angka yang mirip (0/o, 1/l/i).
 */
class TemporaryPassword
{
    private const CHARS = 'abcdefghjkmnpqrstuvwxyz23456789';

    public static function generate(int $length = 8): string
    {
        $max = strlen(self::CHARS) - 1;
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= self::CHARS[random_int(0, $max)];
        }

        return $password;
    }
}
