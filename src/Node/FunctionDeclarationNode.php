<?php
declare(strict_types=1);

namespace SK\Compiler\Node;


class FunctionDeclarationNode extends Node
{
    /**
     * @var string
     */
    private $returnType;

    /**
     * @var []FunctionArgumentNode
     */
    private $argumentList;

    /**
     * Node
     */
    private $body;

    /**
     * @var string
     */
    private $name;

    /**
     * FunctionDeclarationNode constructor.
     * @param string $returnType
     * @param string $name
     * @param array $argumentList
     * @param Node $body
     */
    public function __construct(string $returnType, string $name, array $argumentList, Node $body)
    {
        $this->name = $name;
        $this->returnType = $returnType;
        $this->argumentList = $argumentList;
        $this->body = $body;
    }


}