<?php

namespace NsplTest;

use \nspl\a;

use function \nspl\rnd\sample;
use function \nspl\rnd\choice;
use function \nspl\rnd\weightedChoice;

class RndTest extends \PHPUnit_Framework_TestCase
{
    public function testSample()
    {
        $list = ['a', 'b', 'c', 'd', 'e'];

        $appearances = array_fill_keys($list, 0);
        for ($i = 0; $i < 50000; ++$i) {
            $sample = sample($list, 3);
            foreach ($sample as $item) {
                ++$appearances[$item];
            }
        }

        foreach ($appearances as $item => $rate) {
            $this->assertEquals(30000, $rate, '', 500);
        }

        $this->assertEquals([], sample(['a', 'b', 'c'], 0));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSampleOfPopulationLessThanLength()
    {
        sample(['a', 'b', 'c'], 4);
    }

    public function testChoice()
    {
        $list = ['a', 'b', 'c', 'd', 'e'];

        $appearances = array_fill_keys($list, 0);
        for ($i = 0; $i < 50000; ++$i) {
            ++$appearances[choice($list)];
        }

        foreach ($appearances as $item => $rate) {
            $this->assertEquals(10000, $rate, '', 300);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testChoiceFromEmptySequence()
    {
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
            $this->assertEquals(50000 * $weights[$item] / 100, $rate, '', 300);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWeightedChoiceFromEmptySequence()
    {
        weightedChoice([]);
    }

}
