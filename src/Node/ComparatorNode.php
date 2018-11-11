<?php

namespace SK\Compiler\Node;

class ComparatorNode extends Node
{
    private $operator;
    private $operand1;
    private $operand2;

    public function __construct($operator, Node $operand1, Node $operand2)
    {
        if (!in_array($operator, ["==", "!=", ">", "<", ">=", "=<"])) {
            throw new \Exception();
        }
        $this->operator = $operator;
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
    }

}