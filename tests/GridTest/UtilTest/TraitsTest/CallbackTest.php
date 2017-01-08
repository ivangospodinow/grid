<?php
namespace GridTest\UtilTest\TraitsTest;

use Grid\Util\Traits\Callback;

use PHPUnit\Framework\TestCase;

use \Exception;

class CallbackTest extends TestCase
{
    use Callback;
    
    public function test()
    {
        try {
            $this->call_user_func_array([], []);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        $this->assertTrue($this->call_user_func_array([get_class($this), 'returnCallbackTest']));
    }

    public function returnCallbackTest()
    {
        return true;
    }
}