<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita764baf4fcdbcc5d8ff9f6871a51ff0f
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita764baf4fcdbcc5d8ff9f6871a51ff0f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita764baf4fcdbcc5d8ff9f6871a51ff0f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
