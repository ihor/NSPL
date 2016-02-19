<?php

namespace nspl\ds;

use nspl\args;

class Set extends ArrayObject
{
    public function __construct(/* $e1, $e2, ..., $eN */)
    {
        foreach (func_get_args() as $element) {
            $this->array[$this->getElementKey($element)] = $element;
        }
    }

    /**
     * @param mixed $element
     * @return $this
     */
    public function add($element)
    {
        $this->array[$this->getElementKey($element)] = $element;
        return $this;
    }

    /**
     * @param array|\Traversable $sequence1
     * @param array|\Traversable $sequence2
     * @param ...
     * @param array|\Traversable $sequenceN
     * @return $this
     */
    public function update($sequence1 /*, $sequence2, ..., $sequenceN */)
    {
        foreach (func_get_args() as $position => $sequence) {
            args\expects(args\traversable, $sequence, $position);

            foreach ($sequence as $element) {
                $this->array[$this->getElementKey($element)] = $element;
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
        $elementKey = $this->getElementKey($element);
        return isset($this->array[$elementKey]) || array_key_exists($elementKey, $this->array);
    }

    /**
     * @param mixed $element
     * @return bool
     */
    public function delete($element)
    {
        unset($this->array[$this->getElementKey($element)]);
        return $this;
    }

    /**
     * @param array|\Traversable $sequence
     * @return Set
     */
    public function intersection($sequence)
    {
        args\expects(args\traversable, $sequence);

        $result = new Set();
        foreach ($sequence as $element) {
            $elementKey = $this->getElementKey($element);
            if (isset($this->array[$elementKey])) {
                $result->array[$elementKey] = $element;
            }
        }

        return $result;
    }

    /**
     * @param array|\Traversable $sequence
     * @return Set
     */
    public function difference($sequence)
    {
        args\expects(args\traversable, $sequence);

        $result = new Set();
        $intersection = $this->intersection($sequence);
        foreach ($this->array as $element) {
            $elementKey = $this->getElementKey($element);
            if (!isset($intersection->array[$elementKey])) {
                $result->array[$elementKey] = $element;
            }
        }

        return $result;
    }

    /**
     * @param array|\Traversable $sequence
     * @return Set
     */
    public function union($sequence)
    {
        args\expects(args\traversable, $sequence);

        $result = $this->copy();
        foreach ($sequence as $element) {
            $result->array[$this->getElementKey($element)] = $element;
        }

        return $result;
    }

    /**
     * @param array|\Traversable $sequence
     * @return bool
     */
    public function isSuperset($sequence)
    {
        args\expects(args\traversable, $sequence);

        foreach ($sequence as $element) {
            $elementKey = $this->getElementKey($element);
            if (!isset($this->array[$elementKey]) && !array_key_exists($element, $this->array)) {
                return false;
            }

        }

        return true;
    }

    /**
     * @param array|\Traversable $sequence
     * @return bool
     */
    public function isSubset($sequence)
    {
        args\expects(args\traversable, $sequence);

        $size = count($this->array);
        $present = array();
        foreach ($sequence as $element) {
            $elementKey = $this->getElementKey($element);
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
    protected function getElementKey($element)
    {
        if (is_scalar($element)) {
            return $element;
        }

        return md5(serialize($element));
    }

    //region ArrayAccess methods
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
    public function offsetSet($index, $value)
    {
        if (null === $index) {
            $this->array[$this->getElementKey($value)] = $value;
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
    public function offsetUnset($index)
    {
        throw new \BadMethodCallException('Set does not support indexing');
    }
    //endregion

    //region Iterator methods
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        throw new \BadMethodCallException('Set does not have keys');
    }
    //endregion

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('set(%s)', implode(', ', array_map(function($v) {
            if (is_array($v)) {
                return $this->stringifyArray($v);
            }
            else if (is_object($v)) {
                return get_class($v);
            }

            return var_export($v, true);
        }, $this->array)));
    }

    /**
     * @param array $array
     * @return static
     */
    public static function fromArray(array $array)
    {
        $result = new static();
        foreach ($array as $element) {
            $result[] = $element;
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
}

/**
 * @todo
 *
 * @return Set
 */
function set()
{
    return Set::fromArray(func_get_args());
}
