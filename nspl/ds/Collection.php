<?php

namespace nspl\ds;

use \nspl\a;
use \nspl\f;

abstract class Collection implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * @var array
     */
    protected $array;

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
    public function offsetExists($index)
    {
        return isset($this->array[$index]);
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
    public function &offsetGet($index)
    {
        if (!isset($this->array[$index])) {
            throw new \Exception('Index out of range'); // @todo Throw IndexException
        }

        return $this->array[$index];
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
    public function offsetSet($index, $value)
    {
        if (null === $index) {
            $this->array[] = $value;
        }
        else {
            $this->array[$index] = $value;
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
    public function offsetUnset($index)
    {
        if (!isset($this->array[$index])) {
            throw new \Exception('Index out of range'); // @todo Throw IndexException
        }

        unset($this->array[$index]);
    }
    //endregion

    //region Iterator
    /**
     * @var int
     */
    protected $valid = true;

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->array);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->valid = (bool) next($this->array);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->array);
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
        return $this->valid;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->array);
        $this->valid = true;
    }
    //endregion

    //region Countable
    /**
     * @return int
     */
    public function count()
    {
        return count($this->array);
    }
    //endregion

    //region __toString
    protected function stringifyArray(array $array)
    {
        if (a\isList($array)) {
            return 'array(' . implode(', ', array_map(function($v) {
                return is_scalar($v) ? var_export($v, true) : \nspl\getType($v);
            }, $array)) . ')';
        }

        return f\I(
            var_export($array, true),
            f\partial('str_replace', "\n", ''),
            f\partial('str_replace', 'array (  ', 'array('),
            f\partial('str_replace', '  ', ' '),
            f\partial('str_replace', ',)', ')')
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->stringifyArray($this->array);
    }
    //endregion

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->array;
    }

}
