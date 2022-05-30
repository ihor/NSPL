<?php

namespace nspl\a\lazy;

use nspl\a;

class LazyChainableSequence extends a\ChainableSequence
{
    /**
     * Applies function of one argument to each sequence item
     *
     * @param callable $function
     * @return $this
     */
    public function map(callable $function)
    {
        return new self(map($function, $this->sequence));
    }

    /**
     * Applies function of one argument to each sequence item and flattens the result
     *
     * @param callable $function
     * @return $this
     */
    public function flatMap(callable $function)
    {
        return new self(flatMap($function, $this->sequence));
    }

    /**
     * Zips sequence with a sequence
     *
     * @param iterable $sequence
     * @return $this
     */
    public function zip(iterable $sequence)
    {
        return new self(zip($this->sequence, $sequence));
    }

    /**
     * Generalises zip by zipping with the function given as the first argument
     *
     * @param callable $function
     * @param iterable $sequence
     * @return $this
     */
     public function zipWith(callable $function, iterable $sequence)
     {
         return new self(zipWith($function, $this->sequence, $sequence));
     }

    /**
     * Returns sequence items that satisfy the predicate
     *
     * @param callable $predicate
     * @return $this
     */
    public function filter(callable $predicate)
    {
        return new self(filter($predicate, $this->sequence));
    }

    /**
     * Returns sequence items that don't satisfy the predicate
     *
     * @param callable $predicate
     * @return $this
     */
    public function filterNot(callable $predicate)
    {
        return new self(filterNot($predicate, $this->sequence));
    }

    /**
     * Returns the first N sequence items with the given step
     *
     * @param int $N
     * @param int $step
     * @return $this
     */
    public function take($N, $step = 1)
    {
        return new self(take($this->sequence, $N, $step));
    }

    /**
     * Returns the longest sequence prefix of all items which satisfy the predicate
     *
     * @param callable $predicate
     * @return $this
     */
    public function takeWhile(callable $predicate)
    {
        return new self(takeWhile($predicate, $this->sequence));
    }

    /**
     * Drops the first N sequence items
     *
     * @param int $N
     * @return $this
     */
    public function drop($N)
    {
        return drop($this->sequence, $N);
    }

    /**
     * Drops the longest sequence prefix of all items which satisfy the predicate
     *
     * @param callable $predicate
     * @return $this
     */
    public function dropWhile(callable $predicate)
    {
        return dropWhile($predicate, $this->sequence);
    }

    /**
     * Returns two sequences, one containing values for which the predicate returned true, and the other containing
     * the items that returned false
     *
     * @param callable $predicate
     * @return $this
     */
    public function partition(callable $predicate)
    {
        $result = partition($predicate, $this->sequence);

        return new self([
            new self($result[0]),
            new self($result[1])
        ]);
    }

    /**
     * Flattens multidimensional sequence
     *
     * @param int|null $depth
     * @return $this
     */
    public function flatten($depth = null)
    {
        return new self(flatten($this->sequence, $depth));
    }

    /**
     * Returns a sequence of (key, value) pairs
     *
     * @param bool $valueKey If true then returns (value, key) pairs
     * @return $this
     */
    public function pairs($valueKey = false)
    {
        return new self(pairs($this->sequence, $valueKey));
    }

    /**
     * Returns the sequence keys
     *
     * @return $this
     */
    public function keys()
    {
        return new self(keys($this->sequence));
    }

}
