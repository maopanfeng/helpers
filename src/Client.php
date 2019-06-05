<?php

namespace mxhei\helpers;

class Client
{
    /**
     * @param int $type
     *
     * @return mixed|string
     */
    public static function ip($type = 0)
    {
        $type      = $type ? 1 : 0;
        if (php_sapi_name() === 'cli') {
            return '127.0.0.1';
        }
        $server = $_SERVER;
        $ip = 'unknown';
        if (!empty($server['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $server['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } else {
            $keys = ['HTTP_X_REAL_IP','HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
            foreach ($keys as $key) {
                $ip = getenv($key);
                if ($ip) {
                    break;
                }
            }
        }
        // IP地址类型
        $ip_mode = (strpos($ip, ':') === false) ? 'ipv4' : 'ipv6';
        // IP地址合法验证
        if (filter_var($ip, FILTER_VALIDATE_IP) !== $ip) {
            $ip = ('ipv4' === $ip_mode) ? '0.0.0.0' : '::';
        }
        // 如果是ipv4地址，则直接使用ip2long返回int类型ip；如果是ipv6地址，暂时不支持，直接返回0
        $long_ip = ('ipv4' === $ip_mode) ? sprintf("%u", ip2long($ip)) : 0;
    
        $ip = [$ip, $long_ip];
    
        return $ip[$type];
    }
}