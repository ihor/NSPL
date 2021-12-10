<?php

namespace NsplTest;

use \nspl\a;

use function \nspl\a\isList;
use function \nspl\rnd\randomString;
use function \nspl\rnd\sample;
use function \nspl\rnd\choice;
use function \nspl\rnd\weightedChoice;

class RndTest extends \PHPUnit\Framework\TestCase
{
    public function testRandomString()
    {
        for ($length = 1; $length <= 128; ++$length) {
            for ($j = 0; $j < 8; ++$j) {
                $string = randomString($length);
                $this->assertEquals($length, strlen($string));
            }
        }
    }

    public function testSample()
    {
        $list = ['a', 'b', 'c', 'd', 'e'];
        $this->assertTrue(isList(sample($list, 3)));

        $iterator = new \ArrayIterator($list);
        $this->assertTrue(isList(sample($iterator, 3)));
        foreach (sample($iterator, 3) as $item) {
            $this->assertTrue(in_array($item, $list));
        }

        $sample = sample($list, 3, true);
        foreach ($sample as $k => $element) {
            $this->assertTrue($list[$k] === $element);
        }

        $this->assertEquals([], sample(['a', 'b', 'c'], 0));

        $appearances = array_fill_keys($list, 0);
        for ($i = 0; $i < 50000; ++$i) {
            $sample = sample($list, 3);
            foreach ($sample as $item) {
                ++$appearances[$item];
            }
        }

        foreach ($appearances as $item => $rate) {
            $this->assertEqualsWithDelta(30000, $rate, 500);
        }
    }

    public function testSampleOfPopulationLessThanLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        sample(['a', 'b', 'c'], 4);
    }

    public function testChoice()
    {
        $list = ['a', 'b', 'c', 'd', 'e'];

        $iterator = new \ArrayIterator($list);
        $this->assertTrue(in_array(choice($iterator), $list));

        $appearances = array_fill_keys($list, 0);
        for ($i = 0; $i < 50000; ++$i) {
            ++$appearances[choice($list)];
        }

        foreach ($appearances as $item => $rate) {
            $this->assertEqualsWithDelta(10000, $rate, 300);
        }
    }

    public function testChoiceFromEmptySequence()
    {
        $this->expectException(\InvalidArgumentException::class);
        choice([]);
    }

    public function testWeightedChoice()
    {
        $weights = [
            'a' => 15,
            'b' => 15,
            'c' => 10,
            'd' => 40,
            'e' => 20,
        ];

        $pairs = a\pairs($weights);

        $appearances = array_fill_keys(array_keys($weights), 0);
        for ($i = 0; $i < 50000; ++$i) {
            ++$appearances[weightedChoice($pairs)];
        }

        foreach ($appearances as $item => $rate) {
            $this->assertEqualsWithDelta(50000 * $weights[$item] / 100, $rate, 300);
        }
    }

    public function testWeightedChoiceWithNonIntegerWeights()
    {
        $weights = [
            'a' => 0.15,
            'b' => 0.15,
            'c' => 0.10,
            'd' => 0.40,
            'e' => 0.20,
        ];

        $pairs = a\pairs($weights);

        $appearances = array_fill_keys(array_keys($weights), 0);
        for ($i = 0; $i < 50000; ++$i) {
            ++$appearances[weightedChoice($pairs)];
        }

        foreach ($appearances as $item => $rate) {
            $this->assertEqualsWithDelta(50000 * $weights[$item], $rate, 300);
        }
    }

    public function testWeightedChoiceFromEmptySequence()
    {
        $this->expectException(\InvalidArgumentException::class);
        weightedChoice([]);
    }

}
