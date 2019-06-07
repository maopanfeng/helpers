<?php

class HttpTest extends \PHPUnit\Framework\TestCase
{
    public function testSend()
    {
        $data = [
            'ip' => '63.223.108.42'
        ];
        $url = 'http://ip.taobao.com/service/getIpInfo.php';
        $http = new \mxhei\helpers\Http();
        $result = $http->get($url, $data);
        $this->assertJson($result);
    }
}