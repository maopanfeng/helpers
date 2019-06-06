<?php

use mxhei\helpers\Event;

class EventTest extends \PHPUnit\Framework\TestCase
{
    public function testOn()
    {
        Event::on('test.on', function(){
            error_log('test.on', 3, __DIR__.'/event.log');
        }, 0);
        
        Event::on('test.on', function(){
            error_log('test1.on', 3, __DIR__.'/event.log');
        }, 1);
        
        $this->assertArrayHasKey(0, Event::get('test.on'));
        $this->assertArrayHasKey(1, Event::get('test.on'));
    }
    
    public function testOff()
    {
        Event::on('test.off', function(){
            error_log('test.off', 3, __DIR__.'/event.log');
        },1);
        $this->assertArrayHasKey(1, Event::get('test.off'));
        Event::off('test.off', 1);
        $this->assertArrayNotHasKey(1, Event::get('test.off'));
    }
    
    public function testListen()
    {
        $file = __DIR__.'/event.log';
        if (is_file($file)) {
            unlink($file);
        }
        Event::on('test.listen', function($params, $extra){
            $params['a'] = 2;
            error_log(var_export([$params, $extra, 'test.listen'], true), 3, __DIR__.'/event.log');
        }, 0);
        Event::on('test.listen', function($params, $extra){
            $params['a'] = 3;
            error_log(var_export([$params, $extra, 'test.listen'], true), 3, __DIR__.'/event.log');
        }, 0);
        $params = ['a'=>1, 'b'=>2];
        $extra = 5;
        Event::listen('test.listen', $params, $extra);
        $this->assertFileExists($file);
        if (is_file($file)) {
            unlink($file);
        }
        Event::on('test.listen', function(){
            error_log(1, 3, __DIR__.'/event.log');
        }, true, true);
        Event::listen('test.listen', $params, $extra, true);
        $this->assertFileExists($file);
        $this->assertStringEqualsFile($file, 1);
    }
}