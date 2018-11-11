<?php

namespace SK\Compiler\Node;

class ReturnNode extends Node
{
    private $expr;

    public function __construct(Node $expr)
    {
        $this->expr = $expr;
    }

}