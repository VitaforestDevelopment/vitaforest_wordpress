<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitff0aa1b675726e285f991c503a0c3216
{
    public static $files = array (
        '89ff252b349d4d088742a09c25f5dd74' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/plugin-update-checker.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TelegramBot\\Api\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TelegramBot\\Api\\' => 
        array (
            0 => __DIR__ . '/..' . '/telegram-bot/api/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitff0aa1b675726e285f991c503a0c3216::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitff0aa1b675726e285f991c503a0c3216::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
