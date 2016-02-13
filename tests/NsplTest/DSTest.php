<?php

namespace NsplTest;

use \nspl\ds\ArrayObject;
use \nspl\ds\DefaultArray;
use function \nspl\ds\arrayobject;
use function \nspl\ds\defaultarray;

//region deprecated
use function \nspl\ds\getType;
use function \nspl\ds\isList;
use function \nspl\ds\traversableToArray;
//endregion

class DsTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayObject()
    {
        $array = new ArrayObject(1, 2, 3);
        $this->assertCount(3, $array);
        $this->assertArrayHasKey(0, $array);
        $this->assertEquals(1, $array[0]);
        $this->assertArrayHasKey(1, $array);
        $this->assertEquals(2, $array[1]);
        $this->assertArrayHasKey(2, $array);
        $this->assertEquals(3, $array[2]);

        $array['answer'] = 42;
        $this->assertCount(4, $array);
        $this->assertArrayHasKey('answer', $array);
        $this->assertEquals(42, $array['answer']);

        unset($array[1]);
        $this->assertCount(3, $array);
        $this->assertArrayNotHasKey(1, $array);

        $array = arrayobject(1, 2, 3);
        $this->assertCount(3, $array);
        $this->assertArrayHasKey(0, $array);
        $this->assertEquals(1, $array[0]);
        $this->assertArrayHasKey(1, $array);
        $this->assertEquals(2, $array[1]);
        $this->assertArrayHasKey(2, $array);
        $this->assertEquals(3, $array[2]);

        $array['answer'] = 42;
        $this->assertCount(4, $array);
        $this->assertArrayHasKey('answer', $array);
        $this->assertEquals(42, $array['answer']);

        unset($array[1]);
        $this->assertCount(3, $array);
        $this->assertArrayNotHasKey(1, $array);
    }

    public function testDefaultArray()
    {
        $array = new DefaultArray(0);
        $this->assertEquals(0, $array['apples']);
        $array['apples'] += 2;
        $this->assertEquals(2, $array['apples']);
        $array['bananas'] = 5;
        $this->assertEquals(5, $array['bananas']);
        $array['bananas'] += 5;
        $this->assertEquals(10, $array['bananas']);

        $array = new DefaultArray(function() { return time(); });
        $this->assertEquals(time(), $array['apples'], '', 0.1);

        $array = defaultarray(0);
        $this->assertEquals(0, $array['apples']);
        $array['apples'] += 2;
        $this->assertEquals(2, $array['apples']);
        $array['bananas'] = 5;
        $this->assertEquals(5, $array['bananas']);
        $array['bananas'] += 5;
        $this->assertEquals(10, $array['bananas']);

        $array = defaultarray(function() { return time(); });
        $this->assertEquals(time(), $array['apples'], '', 0.1);

        $array = new DefaultArray(10, array('apples' => 20, 'bananas' => 30));
        $this->assertEquals(10, $array['oranges']);
        $this->assertEquals(20, $array['apples']);
        $this->assertEquals(30, $array['bananas']);

        $array = defaultarray(10, array('apples' => 20, 'bananas' => 30));
        $this->assertEquals(10, $array['oranges']);
        $this->assertEquals(20, $array['apples']);
        $this->assertEquals(30, $array['bananas']);
    }

    //region deprecated
    public function testGetType()
    {
        $this->assertEquals('NULL', getType(null));
        $this->assertEquals('boolean', getType(true));
        $this->assertEquals('integer', getType(1));
        $this->assertEquals('double', getType(1.0));
        $this->assertEquals('array', getType([]));
        $this->assertEquals('string', getType('hello world'));
        $this->assertEquals('stdClass', getType(new \StdClass()));
    }

    public function testIsList()
    {
        $this->assertFalse(isList(1));
        $this->assertFalse(isList(array(1 => 'a')));
        $this->assertFalse(isList(array(0 => 'a', 2 => 'c')));
        $this->assertFalse(isList(new \StdClass()));
        $this->assertTrue(isList([]));
        $this->assertTrue(isList([1]));
        $this->assertTrue(isList([1, 2, 3]));
        $this->assertTrue(isList([10, 11, 13]));
    }
    //endregion
}
