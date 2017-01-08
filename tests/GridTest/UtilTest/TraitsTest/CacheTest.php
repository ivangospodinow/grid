<?php
namespace GridTest\UtilTest\TraitsTest;

use Grid\Util\Traits\Cache;

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    use Cache;
    
    public function test()
    {
        $this->assertTrue($this->getCache('test') === null);
        $this->assertFalse($this->hasCache('test'));
        $this->setCache('test', '123');
        $this->assertTrue($this->getCache('test') === '123');
    }
}