<?php

namespace nspl\ds;

class ArrayObject extends Collection
{
    public function __construct(/* $e1, $e2, ..., $eN */)
    {
        $this->array = func_get_args();
    }

    /**
     * @param array $array
     * @return static
     */
    public static function fromArray(array $array)
    {
        $result = new static();
        $result->array = $array;

        return $result;
    }

    //region __toString
    /**
     * @return string
     */
    public function __toString()
    {
        return 'arrayobject' . substr(parent::__toString(), 5);
    }
    //endregion

}

/**
 * Returns new ArrayObject
 *
 * @return ArrayObject
 */
function arrayobject(/* $e1, $e2, ..., $eN */)
{
    return ArrayObject::fromArray(func_get_args());
}
