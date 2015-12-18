<?php

namespace NsplTest\OpTest;

use \nspl\op;
use function \nspl\op\itemGetter;
use function \nspl\op\propertyGetter;
use function \nspl\op\methodCaller;
use function \nspl\f\map;

class OpTest extends \PHPUnit_Framework_TestCase
{
    public function testSum()
    {
        $this->assertEquals(1, call_user_func(op::$sum, 0, 1));
        $this->assertEquals(5, call_user_func(op::$sum, 2, 3));
    }

    public function testSub()
    {
        $this->assertEquals(3, call_user_func(op::$sub, 5, 2));
        $this->assertEquals(1, call_user_func(op::$sub, 1, 0));
    }

    public function testMul()
    {
        $this->assertEquals(6, call_user_func(op::$mul, 2, 3));
        $this->assertEquals(2, call_user_func(op::$mul, 2, 1));
    }

    public function testDiv()
    {
        $this->assertEquals(2, call_user_func(op::$div, 6, 3));
        $this->assertEquals(2, call_user_func(op::$div, 2, 1));
    }

    public function testMod()
    {
        $this->assertEquals(2, call_user_func(op::$mod, 2, 3));
        $this->assertEquals(0, call_user_func(op::$mod, 6, 2));
    }

    public function testInc()
    {
        $this->assertEquals(3, call_user_func(op::$inc, 2));
    }

    public function testDec()
    {
        $this->assertEquals(1, call_user_func(op::$dec, 2));
    }

    public function testNeg()
    {
        $this->assertEquals(-2, call_user_func(op::$neg, 2));
        $this->assertEquals(0, call_user_func(op::$neg, 0));
    }

    public function testBand()
    {
        $this->assertEquals(E_ALL & ~E_NOTICE, call_user_func(op::$band, E_ALL, ~E_NOTICE));
    }

    public function testBxor()
    {
        $this->assertEquals(E_ALL ^ E_NOTICE, call_user_func(op::$bxor, E_ALL, E_NOTICE));
    }

    public function testBor()
    {
        $this->assertEquals(E_ERROR | E_RECOVERABLE_ERROR, call_user_func(op::$bor, E_ERROR, E_RECOVERABLE_ERROR));
    }

    public function testBnot()
    {
        $this->assertEquals(~E_NOTICE, call_user_func(op::$bnot, E_NOTICE));
    }

    public function testLshift()
    {
        $this->assertEquals(8, call_user_func(op::$lshift, 4, 1));
    }

    public function testRshift()
    {
        $this->assertEquals(2, call_user_func(op::$rshift, 4, 1));
    }

    public function testLt()
    {
        $this->assertTrue(call_user_func(op::$lt, 2, 3));
        $this->assertFalse(call_user_func(op::$lt, 2, 2));
        $this->assertFalse(call_user_func(op::$lt, 6, 3));
    }

    public function testLe()
    {
        $this->assertTrue(call_user_func(op::$le, 2, 3));
        $this->assertTrue(call_user_func(op::$le, 2, 2));
        $this->assertFalse(call_user_func(op::$le, 6, 3));
    }

    public function testGt()
    {
        $this->assertTrue(call_user_func(op::$gt, 3, 2));
        $this->assertFalse(call_user_func(op::$gt, 2, 2));
        $this->assertFalse(call_user_func(op::$gt, 3, 6));
    }

    public function testGe()
    {
        $this->assertTrue(call_user_func(op::$ge, 3, 2));
        $this->assertTrue(call_user_func(op::$ge, 2, 2));
        $this->assertFalse(call_user_func(op::$ge, 3, 6));
    }

    public function testEq()
    {
        $this->assertTrue(call_user_func(op::$eq, 2, 2));
        $this->assertTrue(call_user_func(op::$eq, 2, '2'));
        $this->assertFalse(call_user_func(op::$eq, 3, 6));
    }

    public function testIdnt()
    {
        $this->assertTrue(call_user_func(op::$idnt, 2, 2));
        $this->assertFalse(call_user_func(op::$idnt, 2, '2'));
        $this->assertFalse(call_user_func(op::$idnt, 3, 6));
    }

    public function testNe()
    {
        $this->assertFalse(call_user_func(op::$ne, 2, 2));
        $this->assertFalse(call_user_func(op::$ne, 2, '2'));
        $this->assertTrue(call_user_func(op::$ne, 3, 6));
    }

    public function testNidnt()
    {
        $this->assertFalse(call_user_func(op::$nidnt, 2, 2));
        $this->assertTrue(call_user_func(op::$nidnt, 2, '2'));
        $this->assertTrue(call_user_func(op::$nidnt, 3, 6));
    }

    public function testAnd()
    {
        $this->assertTrue(call_user_func(op::$and, true, true));
        $this->assertFalse(call_user_func(op::$and, false, false));
        $this->assertFalse(call_user_func(op::$and, true, false));
        $this->assertFalse(call_user_func(op::$and, false, true));
    }

    public function testMand()
    {
        $this->assertTrue(call_user_func(op::$mand, true, true));
        $this->assertTrue(call_user_func(op::$mand, false, false));
        $this->assertFalse(call_user_func(op::$mand, true, false));
        $this->assertFalse(call_user_func(op::$mand, false, true));
    }

    public function testOr()
    {
        $this->assertTrue(call_user_func(op::$or, true, true));
        $this->assertFalse(call_user_func(op::$or, false, false));
        $this->assertTrue(call_user_func(op::$or, true, false));
        $this->assertTrue(call_user_func(op::$or, false, true));
    }

    public function testXor()
    {
        $this->assertFalse(call_user_func(op::$xor, true, true));
        $this->assertFalse(call_user_func(op::$xor, false, false));
        $this->assertTrue(call_user_func(op::$xor, true, false));
        $this->assertTrue(call_user_func(op::$xor, false, true));
    }

    public function testNot()
    {
        $this->assertFalse(call_user_func(op::$not, true));
        $this->assertTrue(call_user_func(op::$not, false));
    }

    public function testConcat()
    {
        $this->assertEquals('Hello world', call_user_func(op::$concat, 'Hello ', 'world'));
        $this->assertEquals('12', call_user_func(op::$concat, 1, 2));
    }

    public function testInt()
    {
        $this->assertSame(1, call_user_func(op::$int, '1'));
        $this->assertSame(1, call_user_func(op::$int, 1.0));
    }

    public function testBool()
    {
        $this->assertSame(true, call_user_func(op::$bool, '1'));
        $this->assertSame(false, call_user_func(op::$bool, 0));
    }

    public function testFloat()
    {
        $this->assertSame(1.0, call_user_func(op::$float, '1'));
        $this->assertSame(1.0, call_user_func(op::$float, 1));
    }

    public function testStr()
    {
        $this->assertSame('1', call_user_func(op::$str, 1));
    }

    public function testArray()
    {
        $this->assertSame([1], call_user_func(op::$array, 1));

        $object = new \StdClass();
        $object->hello = 'world';
        $object->answer = 42;

        $this->assertSame(array('hello' => 'world', 'answer' => 42), call_user_func(op::$array, $object));
    }

    public function testObject()
    {
        $object = new \StdClass();
        $object->hello = 'world';
        $object->answer = 42;

        $this->assertEquals($object, call_user_func(op::$object, array('hello' => 'world', 'answer' => 42)));
    }

    public function testItemGetter()
    {
        $first = itemGetter(0);
        $second = itemGetter(1);
        $firstAndSecond = itemGetter(0, 1);

        $this->assertEquals(1, $first([1, 2]));
        $this->assertEquals(2, $second([1, 2]));
        $this->assertEquals([1, 2], $firstAndSecond([1, 2, 3]));

        $users = [
            array('name' => 'John', 'age' => 18),
            array('name' => 'Jack', 'age' => 20),
            array('name' => 'Sarah', 'age' => 19),
        ];

        $this->assertEquals(['John', 'Jack', 'Sarah'], map(itemGetter('name'), $users));
    }

    public function testPropertyGetter()
    {
        $users = [
            new User('John', 18),
            new User('Jack', 20),
            new User('Sarah', 19),
        ];

        $this->assertEquals(['John', 'Jack', 'Sarah'], map(propertyGetter('name'), $users));
        $this->assertEquals([18, 20, 19], map(propertyGetter('age'), $users));
        $this->assertEquals(
            [
                array('name' => 'John', 'age' => 18),
                array('name' => 'Jack', 'age' => 20),
                array('name' => 'Sarah', 'age' => 19),
            ],
            map(propertyGetter('name', 'age'), $users)
        );
    }

    public function testMethodCaller()
    {
        $users = [
            new User('John', 18),
            new User('Jack', 20),
            new User('Sarah', 19),
        ];

        $this->assertEquals(['John', 'Jack', 'Sarah'], map(methodCaller('getName'), $users));
        $this->assertEquals([18, 20, 19], map(methodCaller('getAge'), $users));
        $this->assertEquals([21, 23, 22], map(methodCaller('getAgeIn', [3]), $users));
    }

}

class User
{
    public $name;
    public $age;

    public function __construct($name, $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getAgeIn($years)
    {
        return $this->age + $years;
    }

}
