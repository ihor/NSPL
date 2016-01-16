<?php

namespace NsplTest;

use function \nspl\args\expectsNotEmpty;
use function \nspl\args\expectsBool;
use function \nspl\args\expectsInt;
use function \nspl\args\expectsFloat;
use function \nspl\args\expectsNumeric;
use function \nspl\args\expectsString;
use function \nspl\args\expectsArrayKey;
use function \nspl\args\expectsTraversable;
use function \nspl\args\expectsArrayAccess;
use function \nspl\args\expectsArrayAccessOrString;
use function \nspl\args\expectsArrayKeyOrCallable;
use function \nspl\args\expectsWithMethod;
use function \nspl\args\expectsWithMethods;
use function \nspl\args\expectsWithKeys;
use function \nspl\args\expects;

class ArgsTest extends \PHPUnit_Framework_TestCase
{
    public function testExpectsNotEmpty_Positive()
    {
        function expectsNotEmptyPositiveTest($arg) { expectsNotEmpty($arg); }
        $this->assertNull(expectsNotEmptyPositiveTest(true));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsNotEmptyNegativeTest() must not be empty, 0 given
     */
    public function testExpectsNotEmpty_Negative()
    {
        function expectsNotEmptyNegativeTest($arg) { expectsNotEmpty($arg); }
        $this->assertNull(expectsNotEmptyNegativeTest(0));
    }

    public function testExpectsBool_Positive()
    {
        function expectsBoolPositiveTest($arg) { expectsBool($arg); }
        $this->assertNull(expectsBoolPositiveTest(true));
        $this->assertNull(expectsBoolPositiveTest(false));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsBoolNegativeTest() must be a boolean, integer given
     */
    public function testExpectsBool_Negative()
    {
        function expectsBoolNegativeTest($arg) { expectsBool($arg); }
        $this->assertNull(expectsBoolNegativeTest(1));
    }

    public function testExpectsInt_Positive()
    {
        function expectsIntPositiveTest($arg1, $arg2) { expectsInt($arg2); }
        $this->assertNull(expectsIntPositiveTest(true, 1));
        $this->assertNull(expectsIntPositiveTest('hello world', 0));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to NsplTest\expectsIntNegativeTest() must be an integer, string given
     */
    public function testExpectsInt_Negative()
    {
        function expectsIntNegativeTest($arg1, $arg2) { expectsInt($arg2); }
        $this->assertNull(expectsIntNegativeTest('hello world', '1'));
    }

    public function testExpectsFloat_Positive()
    {
        function expectsFloatPositiveTest($arg1, $arg2) { expectsFloat($arg1); }
        $this->assertNull(expectsFloatPositiveTest(1.0, 'hello'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsFloatNegativeTest() must be a float, string given
     */
    public function testExpectsFloat_Negative()
    {
        function expectsFloatNegativeTest($arg1, $arg2) { expectsFloat($arg1); }
        $this->assertNull(expectsFloatNegativeTest('hello', 'world'));
    }

    public function testExpectsNumeric_Positive()
    {
        function expectsNumericPositiveTest($arg1, $arg2) { expectsNumeric($arg2); }
        $this->assertNull(expectsNumericPositiveTest('answer', 42));
        $this->assertNull(expectsNumericPositiveTest('web', 2.0));
        $this->assertNull(expectsNumericPositiveTest('number -> ', '1'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to NsplTest\expectsNumericNegativeTest() must be numeric, string given
     */
    public function testExpectsNumeric_Negative()
    {
        function expectsNumericNegativeTest($arg1, $arg2) { expectsNumeric($arg2); }
        $this->assertNull(expectsNumericNegativeTest('hello', 'world'));
    }

    public function testExpectsString_Positive()
    {
        function expectsStringPositiveTest($arg1, $arg2) { expectsString($arg2, 2); }
        $this->assertNull(expectsStringPositiveTest(42, 'answer'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to NsplTest\expectsStringNegativeTest() must be a string, integer given
     */
    public function testExpectsString_Negative()
    {
        function expectsStringNegativeTest($arg1, $arg2) { expectsString($arg2, 2); }
        $this->assertNull(expectsStringNegativeTest(42, 42));
    }

    public function testExpectsArrayKey_Positive()
    {
        function expectsArrayKeyPositiveTest($arg1, $arg2) { expectsArrayKey($arg1); }
        $this->assertNull(expectsArrayKeyPositiveTest(42, 'answer'));
        $this->assertNull(expectsArrayKeyPositiveTest('answer', 42));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsArrayKeyNegativeTest() must be an integer or a string, double given
     */
    public function testExpectsArrayKey_Negative()
    {
        function expectsArrayKeyNegativeTest($arg1, $arg2) { expectsArrayKey($arg1); }
        $this->assertNull(expectsArrayKeyNegativeTest(2.0, 2.0));
    }

    public function testExpectsTraversable_Positive()
    {
        function expectsTraversablePositiveTest($arg1) { expectsTraversable($arg1); }
        $this->assertNull(expectsTraversablePositiveTest(array('hello', 'world')));
        $this->assertNull(expectsTraversablePositiveTest(new \ArrayIterator(array('hello', 'world'))));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsTraversableNegativeTest() must be an array or traversable, string given
     */
    public function testExpectsTraversable_Negative()
    {
        function expectsTraversableNegativeTest($arg1) { expectsTraversable($arg1); }
        $this->assertNull(expectsTraversableNegativeTest('hello world'));
    }

    public function testExpectsArrayAccess_Positive()
    {
        function expectsArrayAccessPositiveTest($arg1) { expectsArrayAccess($arg1); }
        $this->assertNull(expectsTraversablePositiveTest(array('hello', 'world')));
        $this->assertNull(expectsTraversablePositiveTest(new \ArrayObject(array('hello', 'world'))));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsArrayAccessNegativeTest() must be an array or implement array access, string given
     */
    public function testExpectsArrayAccess_Negative()
    {
        function expectsArrayAccessNegativeTest($arg1) { expectsArrayAccess($arg1); }
        $this->assertNull(expectsArrayAccessNegativeTest('hello world'));
    }

    public function testExpectsArrayAccessOrString_Positive()
    {
        function expectsArrayAccessOrStringPositiveTest($arg1) { expectsArrayAccessOrString($arg1); }
        $this->assertNull(expectsArrayAccessOrStringPositiveTest(array('hello', 'world')));
        $this->assertNull(expectsArrayAccessOrStringPositiveTest(new \ArrayObject(array('hello', 'world'))));
        $this->assertNull(expectsArrayAccessOrStringPositiveTest('hello world'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsArrayAccessOrStringNegativeTest() must be a string, an array or implement array access, integer given
     */
    public function testExpectsArrayAccessOrString_Negative()
    {
        function expectsArrayAccessOrStringNegativeTest($arg1) { expectsArrayAccessOrString($arg1); }
        $this->assertNull(expectsArrayAccessOrStringNegativeTest(1337));
    }

    public function testExpectsArrayKeyOrCallable_Positive()
    {
        function expectsArrayOrCallablePositiveTest($arg1) { expectsArrayKeyOrCallable($arg1); }
        $this->assertNull(expectsArrayOrCallablePositiveTest(42));
        $this->assertNull(expectsArrayOrCallablePositiveTest('answer'));
        $this->assertNull(expectsArrayOrCallablePositiveTest(function() { return 'answer 42'; }));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsArrayOrCallableNegativeTest() must be an integer or a string or a callable, double given
     */
    public function testExpectsArrayKeyOrCallable_Negative()
    {
        function expectsArrayOrCallableNegativeTest($arg1) { expectsArrayKeyOrCallable($arg1); }
        $this->assertNull(expectsArrayOrCallableNegativeTest(2.0));
    }

    public function testExpectWithMethod_Positive()
    {
        function expectsWithMethodPositiveTest($arg1) { expectsWithMethod($arg1, 'testMethod1'); }
        $this->assertNull(expectsWithMethodPositiveTest(new TestClass()));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsWithMethodNegativeTest() must be an object with public method "test_Method_1", NsplTest\TestClass given
     */
    public function testExpectWithMethod_Negative()
    {
        function expectsWithMethodNegativeTest($arg1) { expectsWithMethod($arg1, 'test_Method_1'); }
        $this->assertNull(expectsWithMethodNegativeTest(new TestClass()));
    }

    public function testExpectWithMethods_Positive()
    {
        function expectsWithMethodsPositiveTest($arg1) { expectsWithMethods($arg1, ['testMethod1', 'testMethod2']); }
        $this->assertNull(expectsWithMethodsPositiveTest(new TestClass()));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsWithMethodsNegativeTest() must be an object with public methods "testMethod1", "test_Method_2", NsplTest\TestClass given
     */
    public function testExpectWithMethods_Negative()
    {
        function expectsWithMethodsNegativeTest($arg1) { expectsWithMethods($arg1, ['testMethod1', 'test_Method_2']); }
        $this->assertNull(expectsWithMethodsNegativeTest(new TestClass()));
    }

    public function testExpectWithKeys_Positive()
    {
        function expectsWithKeysPositiveTest($arg1) { expectsWithKeys($arg1, ['hello', 'answer']); }
        $this->assertNull(expectsWithKeysPositiveTest(array(
            'hello' => 'world',
            'answer' => 42,
        )));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsWithKeysNegativeTest() has to be an array with keys "hello", "answer"
     */
    public function testExpectWithKeys_Negative()
    {
        function expectsWithKeysNegativeTest($arg1) { expectsWithKeys($arg1, ['hello', 'answer']); }
        $this->assertNull(expectsWithKeysNegativeTest(array('hello' => 'world')));
    }

    public function testExpects_Positive()
    {
        function expectsPositiveTest($arg1)
        {
            expects($arg1, 'to be a positive integer', function($arg) {
                return is_int($arg) && $arg > 0;
            });
        }

        $this->assertNull(expectsPositiveTest(42));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to NsplTest\expectsNegativeTest() has to be a positive integer, -1 given
     */
    public function testExpects_Negative()
    {
        function expectsNegativeTest($arg1)
        {
            expects($arg1, 'to be a positive integer', function($arg) {
                return is_int($arg) && $arg > 0;
            });
        }

        $this->assertNull(expectsNegativeTest(-1));
    }

    /**
     * @expectedException \BadFunctionCallException
     * @expectedExceptionMessage Function NsplTest\expectsWithCustomExceptionTest() does not like the given input
     */
    public function testExpectsWithCustomException()
    {
        function expectsWithCustomExceptionTest($arg1)
        {
            expectsBool($arg1, 1, new \BadFunctionCallException(
                'Function NsplTest\expectsWithCustomExceptionTest() does not like the given input'
            ));
        }

        $this->assertNull(expectsWithCustomExceptionTest('true'));
    }

    public function testExpectsExceptionFileAndLine()
    {
        function expectsExceptionFileAndLineTest($arg) { expectsBool($arg); }
        try {
            expectsExceptionFileAndLineTest('true');
        }
        catch (\InvalidArgumentException $e) {
            $this->assertEquals(__FILE__, $e->getFile());
            $this->assertEquals(__LINE__ - 4, $e->getLine());
        }
    }

}

class TestClass
{
    public function testMethod1() {}
    public function testMethod2() {}

}
