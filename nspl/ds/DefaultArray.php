<?php

namespace nspl\ds;

class DefaultArray extends Collection
{
    /**
     * @var mixed|callable
     */
    protected $default;

    /**
     * If you pass a function as default value it will be called without arguments to provide a default value for the given key, this value will be inserted in the array for the key, and returned.
     *
     * @param mixed|callable $default
     * @param array $data Provides initial data
     */
    public function __construct($default, array $data = array())
    {
        $this->default = $default;
        $this->array = $data;
    }

    private function getDefault()
    {
        if (is_callable($this->default)) {
            return call_user_func($this->default);
        }

        return $this->default;
    }

    /**
     * @param mixed $default
     * @param array $array
     * @return static
     */
    public static function fromArray($default, array $array)
    {
        $result = new static($default);
        $result->array = $array;

        return $result;
    }

    //region ArrayAccess
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param int $index <p>
     * The offset to retrieve.
     * </p>
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

    //region __toString
    /**
     * @return string
     */
    public function __toString()
    {
        return 'defaultarray' . substr(parent::__toString(), 5);
    }
    //endregion

}

/**
 * If you pass a function as default value it will be called without arguments to provide a default value for the given key, this value will be inserted in the dictionary for the key, and returned.
 *
 * @param mixed|callable $default
 * @param array $data
 * @return DefaultArray
 */
function defaultarray($default, array $data = array())
{
    return new DefaultArray($default, $data);
}
