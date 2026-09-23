<?php

namespace App\Support;

class AssetVersion
{
    public static function url(string $path): string
    {
        $fullPath = public_path($path);
        $version = is_file($fullPath) ? (string) filemtime($fullPath) : '1';

        return asset($path).'?v='.$version;
    }
}
