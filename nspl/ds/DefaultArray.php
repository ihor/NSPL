<?php

namespace nspl\ds;

class DefaultArray extends ArrayObject
{
    /**
     * @var mixed
     */
    private $default;

    public function __construct($default)
    {
        $this->default = $default;
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
 * @param mixed $default
 * @return DefaultArray
 */
function defaultarray($default)
{
    return new DefaultArray($default);
}
