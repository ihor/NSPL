<?php

namespace nspl\ds;

use nspl\args;

class Set extends Collection
{
    public function __construct(/* $e1, $e2, ..., $eN */)
    {
        $this->array = array();
        foreach (func_get_args() as $element) {
            $this->array[static::getElementKey($element)] = $element;
        }
    }

    /**
     * @param mixed $element
     * @return $this
     */
    public function add($element)
    {
        $this->array[static::getElementKey($element)] = $element;
        return $this;
    }

    /**
     * @param iterable[] ...$sequences
     * @return $this
     */
    public function update(iterable ...$sequences)
    {
        foreach ($sequences as $sequence) {
            foreach ($sequence as $element) {
                $this->array[static::getElementKey($element)] = $element;
            }
        }

        return $this;
    }

    /**
     * @param mixed $element
     * @return bool
     */
    public function contains($element)
    {
        $elementKey = static::getElementKey($element);
        return isset($this->array[$elementKey]) || array_key_exists($elementKey, $this->array);
    }

    /**
     * @param mixed $element
     * @return bool
     */
    public function delete($element)
    {
        unset($this->array[static::getElementKey($element)]);
        return $this;
    }

    /**
     * @param iterable $sequence
     * @return Set
     */
    public function intersection(iterable $sequence)
    {
        if ($sequence instanceof Set) {
            $result = new Set();
            $result->array = array_intersect_key($this->array, $sequence->array);

            return $result;
        }

        $result = new Set();
        foreach ($sequence as $element) {
            $elementKey = static::getElementKey($element);
            if (isset($this->array[$elementKey])) {
                $result->array[$elementKey] = $element;
            }
        }

        return $result;
    }

    /**
     * @param iterable $sequence
     * @return Set
     */
    public function difference(iterable $sequence)
    {
        if ($sequence instanceof Set) {
            $result = new Set();
            $result->array = array_diff_key($this->array, $sequence->array);

            return $result;
        }

        $result = new Set();
        $intersection = $this->intersection($sequence);
        foreach ($this->array as $element) {
            $elementKey = static::getElementKey($element);
            if (!isset($intersection->array[$elementKey])) {
                $result->array[$elementKey] = $element;
            }
        }

        return $result;
    }

    /**
     * @param iterable $sequence
     * @return Set
     */
    public function union(iterable $sequence)
    {
        if ($sequence instanceof Set) {
            $result = new Set();
            $result->array = $this->array + $sequence->array;

            return $result;
        }

        $result = $this->copy();
        foreach ($sequence as $element) {
            $result->array[static::getElementKey($element)] = $element;
        }

        return $result;
    }

    /**
     * @param iterable $sequence
     * @return bool
     */
    public function isSuperset(iterable $sequence)
    {
        if ($sequence instanceof Set) {
            return array_intersect_key($this->array, $sequence->array) === $sequence->array;
        }

        foreach ($sequence as $element) {
            $elementKey = static::getElementKey($element);
            if (!isset($this->array[$elementKey]) && !array_key_exists($element, $this->array)) {
                return false;
            }

        }

        return true;
    }

    /**
     * @param iterable $sequence
     * @return bool
     */
    public function isSubset(iterable $sequence)
    {
        if ($sequence instanceof Set) {
            return array_intersect_key($this->array, $sequence->array) === $this->array;
        }

        $size = count($this->array);
        $present = array();
        foreach ($sequence as $element) {
            $elementKey = static::getElementKey($element);
            if (isset($this->array[$elementKey]) || array_key_exists($element, $this->array)) {
                $present[$element] = true;

                if (count($present) === $size) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->array;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->array = array();
        return $this;
    }

    /**
     * @return Set
     */
    public function copy()
    {
        $result = new Set();
        $result->array = $this->array;

        return $result;
    }

    /**
     * @param mixed $element
     * @return int|string
     */
    protected static function getElementKey($element)
    {
        if (is_int($element) || is_string($element)) {
            return $element;
        }

        return md5(serialize($element));
    }

    /**
     * @param array $array
     * @return static
     */
    public static function fromArray(array $array)
    {
        $result = new static();
        foreach ($array as $element) {
            $result->array[static::getElementKey($element)] = $element;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_values($this->array);
    }

    //region ArrayAccess
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $index <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($index): bool
    {
        throw new \BadMethodCallException('Set does not support indexing');
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param int $index <p>
     * The offset to retrieve.
     * </p>
     * @throws \Exception
     * @return mixed Can return all value types.
     */
    #[\ReturnTypeWillChange]
    public function &offsetGet($index)
    {
        throw new \BadMethodCallException('Set does not support indexing');
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param int $index <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @throws \Exception
     * @return void
     */
    public function offsetSet($index, $value): void
    {
        if (null === $index) {
            $this->array[static::getElementKey($value)] = $value;
        }
        else {
            throw new \BadMethodCallException('Set does not support indexing');
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param int $index <p>
     * The offset to unset.
     * </p>
     * @throws \Exception
     * @return void
     */
    public function offsetUnset($index): void
    {
        throw new \BadMethodCallException('Set does not support indexing');
    }
    //endregion

    //region Iterator
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        throw new \BadMethodCallException('Set does not have keys');
    }
    //endregion

    //region __toString
    /**
     * @return string
     */
    public function __toString()
    {
        return 'set' . substr($this->stringifyArray(array_values($this->array)), 5);
    }
    //endregion

}

/**
 * Returns new Set object
 *
 * @return Set
 */
function set(/* $e1, $e2, ..., $eN */)
{
    return Set::fromArray(func_get_args());
}
