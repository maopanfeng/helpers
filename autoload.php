<?php

class HelpersAutoload
{
    protected static $namespaces = [];
    
    public static function register()
    {
        // 注册自动加载
        spl_autoload_register('HelpersAutoload::autoload', true, true);
    }
    
    /**
     * @param $class
     */
    public static function autoload($class)
    {
        if (isset(static::$namespaces[$class])) {
            $file = static::$namespaces[$class];
        } elseif (strpos($class, '\\') !== false) {
            if (substr($class, 0, 13) === 'mxhei\\helpers') {
                $file = str_replace('\\', DIRECTORY_SEPARATOR ,__DIR__.substr($class, 13).'.php');
                static::$namespaces[$class] = 'src/'.$file;
            }
        }
        if (!empty($file) && file_exists($file)) {
            include $file;
        }
    }
}

HelpersAutoload::register();
