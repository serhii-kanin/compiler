<?php
declare(strict_types=1);

namespace SK\Compiler\Node;


class VarDeclarationNode extends Node
{
    private $type;

    private $id;

    private $assignment;

    /**
     * floatVarDeclaration constructor.
     * @param string $type
     * @param Node $id
     * @param Node|null $assignment
     */
    public function __construct(string $type, Node $id, Node $assignment = null)
    {
        $this->type = $type;
        $this->id = $id;
        $this->assignment = $assignment;
    }
}