<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4ffdff96f0507ff4c680ac56309b1595
{
    public static $files = array (
        '99f3737bfc2506863193fc3311e4fdde' => __DIR__ . '/..' . '/leonardo/collective-class/src/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Collective\\Helpers\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Collective\\Helpers\\' => 
        array (
            0 => __DIR__ . '/..' . '/leonardo/collective-class/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4ffdff96f0507ff4c680ac56309b1595::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4ffdff96f0507ff4c680ac56309b1595::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}