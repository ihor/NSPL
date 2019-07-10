<?php

namespace nspl\a;

use nspl\f;

class ChainableSequence implements \Iterator
{
    /**
     * @var array|\Iterator|\Traversable
     */
    protected $sequence;

    /**
     * @var bool
     */
    private $isArray;

    public function __construct($sequence)
    {
        $this->isArray = is_array($sequence);

        $isValid = $this->isArray ||
            $sequence instanceof \Iterator ||
            $sequence instanceof \IteratorAggregate;

        if (!$isValid) {
            throw new \InvalidArgumentException('ChainableSequence constructor expects array or instance of \Iterator or instance of \IteratorAggregate');
        }

        $this->sequence = $sequence instanceof \IteratorAggregate
            ? $sequence->getIterator()
            : $sequence;
    }

    //region Iterator
    /**
     * @var int
     */
    protected $isValid = true;

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->isArray
            ? current($this->sequence)
            : $this->sequence->current();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ($this->isArray) {
            $this->isValid = (bool) next($this->sequence);
        }
        else {
            $this->sequence->next();
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->isArray
            ? key($this->sequence)
            : $this->sequence->key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->isArray
            ? $this->isValid
            : $this->sequence->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if ($this->isArray) {
            reset($this->sequence);
            $this->isValid = true;
        }
        else {
            $this->sequence->rewind();
        }
    }
    //endregion

    /**
     * Returns true if all of the $sequence items satisfy the predicate (or if the $sequence is empty).
     * If the predicate was not passed returns true if all of the $sequence items are true.
     *
     * @param callable $predicate
     * @return bool
     */
    public function all(callable $predicate = null)
    {
        return all($this->sequence, $predicate);
    }

    /**
     * Returns true if any of the $sequence items satisfy the predicate.
     * If the predicate was not passed returns true if any of the $sequence items are true.
     *
     * @param callable $predicate
     * @return bool
     */
    public function any(callable $predicate = null)
    {
        return any($this->sequence, $predicate);
    }

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
     * @param array|\Traversable $sequence
     * @return $this
     */
    public function zip($sequence)
    {
        return new self(zip($this->sequence, $sequence));
    }

    /**
     * Generalises zip by zipping with the function given as the first argument
     *
     * @param callable $function
     * @param array|\Traversable $sequence
     * @return $this
     */
     public function zipWith(callable $function, $sequence)
     {
         return new self(zipWith($function, $this->sequence, $sequence));
     }

    /**
     * Applies function of two arguments cumulatively to the sequence items, from left to right
     * to reduce the sequence to a single value.
     *
     * @param callable $function
     * @param mixed $initial
     * @return mixed|$this
     */
     public function reduce(callable $function, $initial = 0)
     {
         $result = reduce($function, $this->sequence, $initial);

         return is_array($result)
             ? new self($result)
             : $result;
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
     * Returns sequence containing only the given keys
     *
     * @param array $keys
     * @return $this
     */
    public function takeKeys(array $keys)
    {
        return new self(takeKeys($this->sequence, $keys));
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
     * Returns the first sequence item
     *
     * @return mixed
     */
    public function first()
    {
        return first($this->sequence);
    }

    /**
     * Returns the second sequence item
     *
     * @return mixed
     */
    public function second()
    {
        return second($this->sequence);
    }

    /**
     * Returns the last sequence item
     *
     * @return mixed
     */
    public function last()
    {
        return last($this->sequence);
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
     * Returns array containing all keys except the given ones
     *
     * @param array $keys
     * @return $this
     */
    public function dropKeys(array $keys)
    {
        return dropKeys($this->sequence, $keys);
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
     * Returns two lists, one containing values for which your predicate returned true until the predicate returned
     * false, and the other containing all the items that left
     *
     * @param callable $predicate
     * @return $this
     */
    public function span(callable $predicate)
    {
        $result = span($predicate, $this->sequence);

        return new self([
            new self($result[0]),
            new self($result[1])
        ]);
    }

    /**
     * Returns new sequence which contains indexed sequence items
     *
     * @param int|string|callable $by An array key or a function
     * @param bool $keepLast If true only the last item with the key will be returned otherwise list of items which share the same key value will be returned
     * @param callable|null $transform A function that transforms list item after indexing
     * @return $this
     */
    public function indexed($by, $keepLast = true, callable $transform = null)
    {
        return new self(indexed($this->sequence, $by, $keepLast, $transform));
    }

    /**
     * Returns new sorted sequence
     *
     * @param bool|callable $reversed If true then return reversed sorted sequence. If not boolean and $key was not passed then acts as a $key parameter
     * @param callable $key Function of one argument that is used to extract a comparison key from each item
     * @param callable $cmp Function of two arguments which returns a negative number, zero or positive number depending on
     *                      whether the first argument is smaller than, equal to, or larger than the second argument
     * @return $this
     */
    public function sorted($reversed = false, callable $key = null, callable $cmp = null)
    {
        return new self(sorted($this->sequence, $reversed, $key, $cmp));
    }

    /**
     * Returns new sequence sorted by keys
     *
     * @param bool $reversed
     * @return $this
     */
    public function keySorted($reversed = false)
    {
        return new self(keySorted($this->sequence, $reversed));
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
     * Merges sequence with the given sequence
     *
     * @param array|\Traversable $sequence
     * @return $this
     */
    public function merge($sequence)
    {
        return new self(merge($this->sequence, $sequence));
    }

    /**
     * Moves list item to another position
     *
     * @param int $from
     * @param int $to
     * @return $this
     */
    public function reorder($from, $to)
    {
        return new self(reorder($this->toArray(), $from, $to));
    }

    /**
     * Returns sequence value by key if it exists otherwise returns the default value
     *
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function value($key, $default = null)
    {
        return value($this->toArray(), $key, $default);
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

    /**
     * Returns the sequence values
     *
     * @return $this
     */
    public function values()
    {
        return new self(values($this->sequence));
    }

    /**
     * Checks if the item is present in the sequence
     *
     * @param mixed $item
     * @return bool
     */
    public function contains($item)
    {
        return in($item, $this->sequence);
    }

    /**
     * Computes the difference with the given sequence
     *
     * @param array|\Traversable $sequence
     * @return $this
     */
    public function diff($sequence)
    {
        return new self(diff($this->sequence, $sequence));
    }

    /**
     * Computes the intersection with the given sequence
     *
     * @param array|\Traversable $sequence
     * @return $this
     */
    public function intersect($sequence)
    {
        return new self(intersect($this->sequence, $sequence));
    }

    /**
     * Computes the cartesian product of two or more arrays or traversable objects
     *
     * @param array|\Traversable $sequences
     * @return $this
     */
    public function cartesianProduct()
    {
        $args = func_get_args();
        array_unshift($args, $this->sequence);

        return new self(cartesianProduct($args));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return is_array($this->sequence)
            ? $this->sequence
            : iterator_to_array($this->sequence);
    }

    //region __toString
    /**
     * @return string
     */
    public function __toString()
    {
        $array = $this->toArray();

        if (isList($array)) {
            $itemToString = function($v) {
                return is_scalar($v)
                    ? var_export($v, true)
                    : \nspl\getType($v);
            };

            return '[' . implode(', ', array_map($itemToString, $array)) . ']';
        }

        return f\I(
            var_export($array, true),
            f\partial('str_replace', "\n", ''),
            f\partial('str_replace', 'array (  ', 'array('),
            f\partial('str_replace', '  ', ' '),
            f\partial('str_replace', ',)', ')')
        );
    }
    //endregion

}
