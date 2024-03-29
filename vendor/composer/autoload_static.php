<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6f62252de4c1f86d7ea0410a7cac34a8
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6f62252de4c1f86d7ea0410a7cac34a8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6f62252de4c1f86d7ea0410a7cac34a8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6f62252de4c1f86d7ea0410a7cac34a8::$classMap;

        }, null, ClassLoader::class);
    }
}
