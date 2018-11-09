<?php

namespace SK\Compiler\Node;


class StatementNode extends Node
{
    private $previousStatement;
    private $nextStatement;

    public function __construct(Node $previousStatement, Node $nextStatement)
    {
        $this->previousStatement = $previousStatement;
        $this->nextStatement = $nextStatement;
    }
}
