<?php

namespace nspl\ds;

class DefaultArray extends ArrayObject
{
    /**
     * @var mixed|callable
     */
    private $default;

    /**
     * If you pass a function as default value it will be called without arguments to provide a default value for the given key, this value will be inserted in the array for the key, and returned.
     *
     * @param mixed|callable $default
     * @param array|null $data Provides initial data
     */
    public function __construct($default, $data = array())
    {
        $this->default = $default;
        $this->array = (array) $data;
    }

    private function getDefault()
    {
        if (is_callable($this->default)) {
            return call_user_func($this->default);
        }

        return $this->default;
    }

    //region ArrayAccess methods
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
        if (!$this->offsetExists($index)) {
            $this->offsetSet($index, $this->getDefault());
        }

        return parent::offsetGet($index);
    }
    //endregion
}

/**
 * If you pass a function as default value it will be called without arguments to provide a default value for the given key, this value will be inserted in the dictionary for the key, and returned.
 *
 * @param mixed|callable $default
 * @param array|null $data
 * @return DefaultArray
 */
function defaultarray($default, $data = array())
{
    return new DefaultArray($default, $data);
}
