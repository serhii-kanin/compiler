<?php


namespace SK\Compiler\Node;


class WhileNode extends Node
{
    private $head;
    private $body;

    public function __construct(Node $head, Node $body)
    {
        $this->head = $head;
        $this->body = $body;
    }

}