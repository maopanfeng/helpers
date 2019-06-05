<?php

namespace mxhei\helpers;

class Str
{
    public static $encoding = 'UTF-8';
    
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];
    
    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];
    
    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];
    
    /**
     * Return the length of the given string.
     *
     * @param string $value
     * @param string $encoding
     *
     * @return int
     */
    public static function length($value, $encoding = null)
    {
        if ($encoding !== null) {
            return mb_strlen($value, $encoding);
        }
        
        return mb_strlen($value);
    }
    
    /**
     * @param $str
     * @return int
     */
    public static function byte($str)
    {
        return mb_strlen($str, '8bit');
    }
    
    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, static::$encoding);
    }
    
    /**
     * Convert the given string to upper-case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, static::$encoding);
    }
    
    /**
     * Convert the given string to title case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, static::$encoding);
    }
    
    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;
        
        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }
        
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        
        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }
    
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     *
     * @throws \Exception
     *
     * @return string
     */
    public static function random($length = 16)
    {
        $string = '';
        $random = function_exists('random_bytes');
        
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            
            $bytes = $random ? random_bytes($size) : mt_rand();
            
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        
        return $string;
    }
    
    /**
     * Convert string's encoding.
     *
     * @author yansongda <me@yansonga.cn>
     *
     * @param string $string
     * @param string $to
     * @param string $from
     *
     * @return string
     */
    public static function encoding($string, $to = 'utf-8', $from = 'gb2312')
    {
        return mb_convert_encoding($string, $to, $from);
    }
}