<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0b0d9763d702849142dbef84f603f7eb
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0b0d9763d702849142dbef84f603f7eb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0b0d9763d702849142dbef84f603f7eb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}