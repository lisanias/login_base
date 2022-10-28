<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit672a94d2bc95eeeaede82cd50c115eb9
{
    public static $files = array (
        'd57072e4aa16f0a2002c08a4ef014827' => __DIR__ . '/../..' . '/source/Config.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Source\\' => 7,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Source\\' => 
        array (
            0 => __DIR__ . '/../..' . '/source',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit672a94d2bc95eeeaede82cd50c115eb9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit672a94d2bc95eeeaede82cd50c115eb9::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit672a94d2bc95eeeaede82cd50c115eb9::$classMap;

        }, null, ClassLoader::class);
    }
}