<?php

class ArrTest extends \PHPUnit\Framework\TestCase
{
    protected $arr = [
        'a' => 1,
        'b' => 2,
        3,
        'd' => [
            'a' => 1,
            'b' => [
                'c' => 2
            ]
        ]
    ];
    
    public function testSet()
    {
        $arr = $this->arr;
        $this->assertArrayHasKey('e', \mxhei\helpers\Arr::set($arr, 'e', 1));
        $this->assertEquals($arr['e'], 1);
    }
    
    public function testGet()
    {
        $arr = $this->arr;
        $this->assertEquals(3, \mxhei\helpers\Arr::get($arr, 0));
        $this->assertEquals(2, \mxhei\helpers\Arr::get($arr, 'b'));
        $this->assertArrayHasKey('a', \mxhei\helpers\Arr::get($arr, 'd'));
        $this->assertNull(\mxhei\helpers\Arr::get($arr, 'a.c'));
        $this->assertEquals(2, \mxhei\helpers\Arr::get($arr, 'd.b.c'));
        $this->assertEquals(5, \mxhei\helpers\Arr::get($arr, 'd.b.d', 5));
    }
    
    public function testRemove()
    {
        $arr = $this->arr;
        $this->assertEquals(3, \mxhei\helpers\Arr::get($arr, 0));
        $this->assertArrayHasKey(0, $arr);
        \mxhei\helpers\Arr::remove($arr, 0);
        $this->assertArrayNotHasKey(0, $arr);
        $this->assertArrayHasKey('c', \mxhei\helpers\Arr::get($arr, 'd.b'));
        \mxhei\helpers\Arr::remove($arr, 'd.b.c');
        $this->assertArrayNotHasKey('c', $arr);
    }
    
    public function testMerge()
    {
        $arr = $this->arr;
        $brr = [
            5,
            'a' => 2,
            'c' => [
                'a' => 1,
            ],
            'd' => [
                'a' => 2,
                'd' => 1
            ]
        ];
        $crr = [
            6,
            'e' => ['a'=>1],
            'd' => [
                1
            ]
        ];
        $res = \mxhei\helpers\Arr::merge($arr, $brr, $crr);
        $this->assertArrayHasKey(2, $res);
        $res = \mxhei\helpers\Arr::mergeRecursive($arr, $brr, $crr);
        $this->assertEquals(2, \mxhei\helpers\Arr::get($res, 'd.a'));
        $this->assertArrayHasKey('d', \mxhei\helpers\Arr::get($res, 'd'));
        $this->assertArrayHasKey(0, \mxhei\helpers\Arr::get($res, 'd'));
    }
}