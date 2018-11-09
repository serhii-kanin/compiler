<?php

namespace SK\Compiler\Node;

class IfNode
{
    private $expression;
    private $statement;

    public function __construct(Node $expression, Node $statement)
    {
        $this->expression = $expression;
        $this->statement = $statement;
    }
}