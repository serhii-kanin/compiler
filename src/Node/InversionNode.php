<?php
declare(strict_types=1);

namespace SK\Compiler\Node;


class InversionNode extends Node
{
    /**
     * @var Node
     */
    private $expression;

    /**
     * InversionNode constructor.
     * @param Node $expression
     */
    public function __construct(Node $expression)
    {
        $this->expression = $expression;
    }
}
