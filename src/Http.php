<?php

namespace mxhei\helpers;

class Http
{
    const EVENT_BEFORE_REQUEST = 'http.request.before';
    const EVENT_AFTER_REQUEST = 'http.request.after';
    
    /**
     * @var null|static
     */
    protected static $inst = null;
    /**
     * @var array $headers 默认header
     */
    protected $headers = [];
    
    protected $events = [
        self::EVENT_BEFORE_REQUEST => [],
        self::EVENT_AFTER_REQUEST => [],
    ];
    
    /**
     * @var array $options
     */
    protected $options = [
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ];
    
    /**
     * Http constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options += $options;
        static::$inst = $this;
    }
    
    /**
     * @param array $options
     *
     * @return null|static
     */
    public static function inst($options = [])
    {
        if ( !static::$inst ) {
            static::$inst = new static($options);
        }
        
        return static::$inst;
    }
    
    public function get($url, $data = [], $options = [])
    {
        return $this->send($url, $data, 'GET', $options);
    }
    
    public function post($url, $data = [], $options = [])
    {
        return $this->send($url, $data, 'POST', $options);
    }
    
    public function send($url, $data = [], $method = 'POST', $options = [])
    {
        $method = strtolower($method);
        if ($data && ($method == 'get' || $method == 'head')) {
            $url .= stripos($url, '?') !== false ? '&'.http_build_query($data) : '?'.http_build_query($data);
        }
        $options[CURLOPT_URL] = $url;
        $options = $this->resolveOptions($options);
        $options = $this->resetRequestMethodOption($method, $data, $options);
        Event::listen(static::EVENT_BEFORE_REQUEST, $options, $data);
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        Event::listen(static::EVENT_AFTER_REQUEST, $result, ['data'=>$data, 'error'=>$errno]);
        
        return $result;
    }
    
    protected function resetRequestMethodOption($method, $data, $options)
    {
        $method = strtolower($method);
        switch($method){
            case 'get':
                $options[CURLOPT_HTTPGET] = true;
                break;
            case 'post':
                $options[CURLOPT_POST] = true;
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
                break;
        }
        if ($method == 'put' || $method == 'post') {
            $options[CURLOPT_POSTFIELDS] = $this->buildQuery($data);
        }
        
        return $options;
    }
    
    public function resolveOptions($options = [])
    {
        if (!empty($options[CURLOPT_HTTPHEADER])) {
            $headers = $options[CURLOPT_HTTPHEADER];
            $headers = array_merge($this->headers, $headers);
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        $options = $options+$this->options;
        
        return $options;
    }
    
    protected function buildQuery($data, $prefix = [])
    {
        if (!is_array($data)) {
            return $data;
        }
        $query = [];
        foreach($data as $k=>$v){
            array_push($prefix, $k);
            if (is_array($v)) {
                $query = array_merge($query, $this->buildQuery($v, $prefix));
            } else {
                $newkey = $this->buildPrefix($prefix);
                $query[$newkey] = $v;
            }
            array_pop($prefix);
        }
        return $query;
    }
    
    protected function buildPrefix($prefix)
    {
        $return = '';
        foreach($prefix as $k=>$v){
            if ($k == 0) {
                $return .= $v;
            } else {
                $return .= '['.$v.']';
            }
        }
        
        return $return;
    }
    
    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $inst = static::inst();
        
        return call_user_func_array([$inst, $name], $arguments);
    }
}
