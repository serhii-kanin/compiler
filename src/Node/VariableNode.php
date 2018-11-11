<?php
declare(strict_types=1);

namespace SK\Compiler\Node;


class VariableNode extends Node
{
    private $variableName;

    public function __construct(string $variableName)
    {
        $this->variableName = $variableName;
    }
}