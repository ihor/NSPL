<?php

namespace NsplTest\Ds;

use \nspl\ds\Dstring;
use function \nspl\ds\dstring;

class DstringTest extends \PHPUnit_Framework_TestCase
{
    public function testAddStr()
    {
        $string = (new Dstring())
            ->addStr('Hello')
            ->addStr(' ')
            ->addStr('world!');

        $this->assertEquals('Hello world!', $string);
    }

    public function testAddConstant()
    {
        $string = (new Dstring())
            ->addStr('You are on ')
            ->addConstant('APPLICATION_ENV')
            ->addStr(' environment');

        define('APPLICATION_ENV', 'production');

        $this->assertEquals('You are on production environment', $string);
    }

    public function testAddFunction()
    {
        $string = (new Dstring())
            ->addStr('[')
            ->addFunction('date', ['Y-m-d H:i'])
            ->addStr('] ')
            ->addStr('You are on ')
            ->addConstant('APPLICATION_ENV')
            ->addStr(' environment');

        $this->assertEquals(sprintf('[%s] You are on production environment', date('Y-m-d H:i')), $string);
    }

    public function testDstring()
    {
        $string = dstring()
            ->addStr('[')
            ->addFunction('date', ['Y-m-d H:i'])
            ->addStr('] ')
            ->addStr('You are on ')
            ->addConstant('APPLICATION_ENV')
            ->addStr(' environment');

        $this->assertEquals(sprintf('[%s] You are on production environment', date('Y-m-d H:i')), $string);
    }

}
