<?php

namespace nspl\a;

class ChainableArray extends ChainableSequence
{
    public function __construct($sequence)
    {
        if (!is_array($sequence)) {
            throw new \InvalidArgumentException('ChainableArray constructor expects array');
        }

        parent::__construct($sequence);
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
    public function offsetExists($index)
    {
        return isset($this->sequence[$index]);
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
        if (!isset($this->sequence[$index])) {
            throw new \Exception('Index out of range'); // @todo Throw IndexException
        }

        return $this->sequence[$index];
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
            $this->sequence[] = $value;
        }
        else {
            $this->sequence[$index] = $value;
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
        if (!isset($this->sequence[$index])) {
            throw new \Exception('Index out of range'); // @todo Throw IndexException
        }

        unset($this->sequence[$index]);
    }
    //endregion

    //region Countable
    /**
     * @return int
     */
    public function count()
    {
        return count($this->sequence);
    }
    //endregion

}
