<?php

namespace SK\Compiler\Node;

class LogicalOperatorNode extends Node
{
    private $operator;
    private $operand1;
    private $operand2;

    public function __construct(string $operator, Node $operand1, Node $operand2)
    {
        $this->operator = $operator;
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
    }
}