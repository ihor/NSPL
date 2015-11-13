<?php

namespace nspl\ds;

class Dstring
{
    /**
     * @var array
     */
    private $parts = array();

    /**
     * @param string $string
     * @return $this
     */
    public function addStr($string)
    {
        $this->parts[] = array('string', $string);
        return $this;
    }

    /**
     * @param string $constantName
     * @return $this
     */
    public function addConstant($constantName)
    {
        $this->parts[] = array('constant', $constantName);
        return $this;
    }

    /**
     * @param callable $function
     * @param array $args
     * @return $this
     */
    public function addFunction($function, array $args = array())
    {
        $this->parts[] = array('function', $function, $args);
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return array_reduce($this->parts, function($result, $part) {
            switch ($part[0]) {
                case 'string': return $result . $part[1];
                case 'constant': return $result . constant($part[1]);
                case 'function': return $result . call_user_func_array($part[1], $part[2]);
                default: return $result;
            }
        }, '');
    }

}

/**
 * @return Dstring
 */
function dstring()
{
    return new Dstring();
}
