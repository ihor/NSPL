<?php

namespace NsplTest\OpTest;

use const nspl\f\apply;
use const \nspl\op\sum;
use const \nspl\op\sub;
use const \nspl\op\mul;
use const \nspl\op\div;
use const \nspl\op\mod;
use const \nspl\op\inc;
use const \nspl\op\dec;
use const \nspl\op\neg;
use const \nspl\op\band;
use const \nspl\op\bor;
use const \nspl\op\bxor;
use const \nspl\op\bnot;
use const \nspl\op\lshift;
use const \nspl\op\rshift;
use const \nspl\op\lt;
use const \nspl\op\le;
use const \nspl\op\gt;
use const \nspl\op\ge;
use const \nspl\op\eq;
use const \nspl\op\spaceship;
use const \nspl\op\idnt;
use const \nspl\op\nidnt;
use const \nspl\op\ne;
use const \nspl\op\and_;
use const \nspl\op\or_;
use const \nspl\op\xor_;
use const \nspl\op\not;
use const \nspl\op\concat;
use const \nspl\op\int;
use const \nspl\op\float;
use const \nspl\op\str;
use const \nspl\op\bool;
use const \nspl\op\object;
use const \nspl\op\array_;
use const \nspl\op\instanceOf_;

use function \nspl\op\itemGetter;
use function \nspl\op\propertyGetter;
use function \nspl\op\methodCaller;
use function nspl\op\instanceCreator;
use function nspl\op\instanceOf_;
use function \nspl\a\map;
use function nspl\f\partial;

class OpTest extends \PHPUnit\Framework\TestCase
{
    public function testSum()
    {
        $this->assertEquals(1, call_user_func(sum, 0, 1));
        $this->assertEquals(5, call_user_func(sum, 2, 3));
    }

    public function testSub()
    {
        $this->assertEquals(3, call_user_func(sub, 5, 2));
        $this->assertEquals(1, call_user_func(sub, 1, 0));
    }

    public function testMul()
    {
        $this->assertEquals(6, call_user_func(mul, 2, 3));
        $this->assertEquals(2, call_user_func(mul, 2, 1));
    }

    public function testDiv()
    {
        $this->assertEquals(2, call_user_func(div, 6, 3));
        $this->assertEquals(2, call_user_func(div, 2, 1));
    }

    public function testMod()
    {
        $this->assertEquals(2, call_user_func(mod, 2, 3));
        $this->assertEquals(0, call_user_func(mod, 6, 2));
    }

    public function testInc()
    {
        $this->assertEquals(3, call_user_func(inc, 2));
        $this->assertEquals(3, call_user_func(inc, 2));
    }

    public function testDec()
    {
        $this->assertEquals(1, call_user_func(dec, 2));
        $this->assertEquals(1, call_user_func(dec, 2));
    }

    public function testNeg()
    {
        $this->assertEquals(-2, call_user_func(neg, 2));
        $this->assertEquals(0, call_user_func(neg, 0));
    }

    public function testBand()
    {
        $this->assertEquals(E_ALL & ~E_NOTICE, call_user_func(band, E_ALL, ~E_NOTICE));
    }

    public function testBxor()
    {
        $this->assertEquals(E_ALL ^ E_NOTICE, call_user_func(bxor, E_ALL, E_NOTICE));
    }

    public function testBor()
    {
        $this->assertEquals(E_ERROR | E_RECOVERABLE_ERROR, call_user_func(bor, E_ERROR, E_RECOVERABLE_ERROR));
    }

    public function testBnot()
    {
        $this->assertEquals(~E_NOTICE, call_user_func(bnot, E_NOTICE));
    }

    public function testLshift()
    {
        $this->assertEquals(8, call_user_func(lshift, 4, 1));
    }

    public function testRshift()
    {
        $this->assertEquals(2, call_user_func(rshift, 4, 1));
    }

    public function testLt()
    {
        $this->assertTrue(call_user_func(lt, 2, 3));
        $this->assertFalse(call_user_func(lt, 2, 2));
        $this->assertFalse(call_user_func(lt, 6, 3));
    }

    public function testLe()
    {
        $this->assertTrue(call_user_func(le, 2, 3));
        $this->assertTrue(call_user_func(le, 2, 2));
        $this->assertFalse(call_user_func(le, 6, 3));
    }

    public function testGt()
    {
        $this->assertTrue(call_user_func(gt, 3, 2));
        $this->assertFalse(call_user_func(gt, 2, 2));
        $this->assertFalse(call_user_func(gt, 3, 6));
    }

    public function testGe()
    {
        $this->assertTrue(call_user_func(ge, 3, 2));
        $this->assertTrue(call_user_func(ge, 2, 2));
        $this->assertFalse(call_user_func(ge, 3, 6));
    }

    public function testEq()
    {
        $this->assertTrue(call_user_func(eq, 2, 2));
        $this->assertTrue(call_user_func(eq, 2, '2'));
        $this->assertFalse(call_user_func(eq, 3, 6));
    }

    public function testIdnt()
    {
        $this->assertTrue(call_user_func(idnt, 2, 2));
        $this->assertFalse(call_user_func(idnt, 2, '2'));
        $this->assertFalse(call_user_func(idnt, 3, 6));
    }

    public function testNe()
    {
        $this->assertFalse(call_user_func(ne, 2, 2));
        $this->assertFalse(call_user_func(ne, 2, '2'));
        $this->assertTrue(call_user_func(ne, 3, 6));
    }

    public function testNidnt()
    {
        $this->assertFalse(call_user_func(nidnt, 2, 2));
        $this->assertTrue(call_user_func(nidnt, 2, '2'));
        $this->assertTrue(call_user_func(nidnt, 3, 6));
    }

    public function testSpaceship()
    {
        $this->assertSame(0, call_user_func(spaceship, 5, 5));
        $this->assertSame(1, call_user_func(spaceship, 3, 2));
        $this->assertSame(-1, call_user_func(spaceship, 2, 3));
    }

    public function testAnd()
    {
        $this->assertTrue(call_user_func(and_, true, true));
        $this->assertFalse(call_user_func(and_, false, false));
        $this->assertFalse(call_user_func(and_, true, false));
        $this->assertFalse(call_user_func(and_, false, true));
    }

    public function testOr()
    {
        $this->assertTrue(call_user_func(or_, true, true));
        $this->assertFalse(call_user_func(or_, false, false));
        $this->assertTrue(call_user_func(or_, true, false));
        $this->assertTrue(call_user_func(or_, false, true));
    }

    public function testXor()
    {
        $this->assertFalse(call_user_func(xor_, true, true));
        $this->assertFalse(call_user_func(xor_, false, false));
        $this->assertTrue(call_user_func(xor_, true, false));
        $this->assertTrue(call_user_func(xor_, false, true));
    }

    public function testNot()
    {
        $this->assertFalse(call_user_func(not, true));
        $this->assertTrue(call_user_func(not, false));
    }

    public function testConcat()
    {
        $this->assertEquals('Hello world', call_user_func(concat, 'Hello ', 'world'));
        $this->assertEquals('12', call_user_func(concat, 1, 2));
    }

    public function testInt()
    {
        $this->assertSame(1, call_user_func(int, '1'));
        $this->assertSame(1, call_user_func(int, 1.0));
    }

    public function testBool()
    {
        $this->assertSame(true, call_user_func(bool, '1'));
        $this->assertSame(false, call_user_func(bool, 0));
    }

    public function testFloat()
    {
        $this->assertSame(1.0, call_user_func(float, '1'));
        $this->assertSame(1.0, call_user_func(float, 1));
    }

    public function testStr()
    {
        $this->assertSame('1', call_user_func(str, 1));
        $this->assertSame('1', call_user_func(str, 1));
    }

    public function testArray()
    {
        $this->assertSame([1], call_user_func(array_, 1));
        $this->assertSame([1], call_user_func(array_, 1));

        $object = new \StdClass();
        $object->hello = 'world';
        $object->answer = 42;

        $this->assertSame(array('hello' => 'world', 'answer' => 42), call_user_func(array_, $object));
    }

    public function testObject()
    {
        $object = new \StdClass();
        $object->hello = 'world';
        $object->answer = 42;

        $this->assertEquals($object, call_user_func(object, array('hello' => 'world', 'answer' => 42)));
        $this->assertEquals($object, call_user_func(object, array('hello' => 'world', 'answer' => 42)));
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

    public function testInstanceCreatorSingleParameter()
    {
        $userNames = [
            'John',
            'Jack',
            'Sarah',
        ];

        $users = map(instanceCreator(User::class), $userNames);
        $this->assertEquals(['John', 'Jack', 'Sarah'], map(methodCaller('getName'), $users));
    }

    public function testInstanceCreatorVariadicConstructor() {
        $userData = [
            ['John', 18],
            ['Jack', 20],
            ['Sarah', 19],
        ];

        $users = map(partial(apply, instanceCreator(User::class)), $userData);

        $this->assertEquals(['John', 'Jack', 'Sarah'], map(methodCaller('getName'), $users));
        $this->assertEquals([18, 20, 19], map(methodCaller('getAge'), $users));
        $this->assertEquals([21, 23, 22], map(methodCaller('getAgeIn', [3]), $users));
    }

    public function testInstanceOf()
    {
        $object = new \StdClass();
        $user = new User('John');

        $this->assertTrue(instanceOf_($user, User::class));
        $this->assertTrue(instanceOf_($object, \StdClass::class));
        $this->assertFalse(instanceOf_($user, \StdClass::class));
        $this->assertFalse(instanceOf_($user, 'random string'));
        $this->assertFalse(instanceOf_($object, User::class));

        $this->assertTrue(call_user_func(instanceOf_, $user, User::class));
        $this->assertFalse(call_user_func(instanceOf_, $user, \StdClass::class));
    }

}

class User
{
    public $name;
    public $age;

    public function __construct($name, $age=0)
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
