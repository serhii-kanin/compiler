<?php
declare(strict_types=1);

namespace SK\Compiler\Node;


class FunctionArgumentNode extends Node
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $id;

    /**
     * FunctionArgumentNode constructor.
     * @param string $type
     * @param string $id
     */
    public function __construct(string $type, string $id)
    {
        $this->type = $type;
        $this->id = $id;
    }
}
