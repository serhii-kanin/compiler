<?php

namespace SK\Compiler\Node;

class ConstNode extends Node{
    private $value;

    /**
     * ConstNode constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

}
