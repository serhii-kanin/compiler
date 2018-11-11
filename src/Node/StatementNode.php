<?php

namespace SK\Compiler\Node;


class StatementNode extends Node
{
    private $statement1;
    private $statement2;

    public function __construct(Node $statement1, Node $statement2)
    {
        $this->statement1 = $statement1;
        $this->statement2 = $statement2;
    }
}
