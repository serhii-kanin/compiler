<?php
declare(strict_types=1);

namespace SK\Compiler\Node;

/**
 * if (<expr>) { <stmt> } | if (<expr>) { <stmt> } else { <stmt> }
 *
 * Class ConditionNode
 * @package SK\Compiler\Node
 */
class ConditionNode extends Node
{
    /**
     * @var Node
     */
    private $expression;

    /**
     * @var Node
     */
    private $trueStatement;

    /**
     * @var Node
     */
    private $falseStatement;

    public function __construct(Node $expression, Node $trueStatement, Node $falseStatement = null)
    {
        $this->expression = $expression;
        $this->trueStatement = $trueStatement;
        $this->falseStatement = $falseStatement;
    }
}