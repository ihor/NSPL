<?php

namespace nspl\ds;

class Rstring
{
    /**
     * @var array
     */
    private $parts = array();

    /**
     * @var array
     */
    private $placeholders = array();

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
     * @param string $string
     * @return $this
     */
    public function addStr($string)
    {
        $this->parts[] = array('string', $string);
        return $this;
    }

    /**
     * @param callable $callback
     * @param array $args
     * @return $this
     */
    public function addFun($callback, array $args = array())
    {
        $this->parts[] = array('callback', $callback, $args);
        return $this;
    }

    /**
     * Sprintf placeholder
     *
     * @param string $placeholder
     * @return $this
     */
    public function addPlaceholder($placeholder)
    {
        $this->parts[] = array('placeholder', $placeholder);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function pushPlaceholderValue($value)
    {
        array_push($this->placeholders, $value);
        return $this;
    }

    /**
     * @return callable
     */
    public function asFunction()
    {
        $self = $this;
        return function() use ($self) {
            foreach (func_get_args() as $arg) {
                $self->pushPlaceholderValue($arg);
            }

            return (string) $self;
        };
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $placeholders = &$this->placeholders;
        return array_reduce($this->parts, function($result, $part) use (&$placeholders) {
            switch ($part[0]) {
                case 'string': return $result . $part[1];
                case 'constant': return $result . constant($part[1]);
                case 'callback': return $result . call_user_func_array($part[1], $part[2]);
                case 'placeholder': return $result . sprintf($part[1], array_pop($placeholders));
                default: return $result;
            }
        }, '');
    }

}

/**
 * @return Rstring
 */
function rstring()
{
    return new Rstring();
}
