<?php

namespace SK\Compiler;


use SK\Compiler\Node\ConstNode;
use SK\Compiler\Node\IfNode;
use SK\Compiler\Node\Node;
use SK\Compiler\Node\NullNode;
use SK\Compiler\Node\OperatorNode;
use SK\Compiler\Node\StatementNode;

class Parser
{
    private $lexer;

    /**
     * Parser constructor.
     * @param Lexer $lexer
     * @throws \Exception
     */
    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->lexer->goNext();
    }

    /**
     * @throws \Exception
     */
    public function parse()
    {
        print_r($this->stmt());
    }


    /**
     * @return Node
     * @throws \Exception
     */
    private function stmt(): Node
    {
        if ($this->lexer->getToken()['type'] == Lexer::TYPE_EOF) {
            return new NullNode();
        }

        if ($this->lexer->getToken()['type'] == Lexer::TYPE_IF) {
            $this->lexer->goNext();
            if ($this->lexer->getToken()['type'] != Lexer::TYPE_LEFT_ROUND_BRACKET) {
                throw new \Exception('( expected');
            }

            $this->lexer->goNext();
            $expression = $this->expr();
            if ($this->lexer->getToken()['type'] != Lexer::TYPE_RIGHT_ROUND_BRACKET) {
                throw new \Exception(') expected');
            }

            if ($this->lexer->getToken()['type'] != Lexer::TYPE_LEFT_CURLY_BRACKET) {
                throw new \Exception('{ excepted');
            }

            $ifStatement = $this->stmt();

            if ($this->lexer->getToken()['type'] != Lexer::TYPE_RIGHT_CURLY_BRACKET) {
                throw new \Exception('} excepted');
            }

            $statement = new IfNode($expression, $ifStatement);
        } else {

            $statement = $this->expr();

        }

        if ($this->lexer->getToken()['type'] == Lexer::TYPE_SEMICOLON) {
            $this->lexer->goNext();
        }

        return new StatementNode($statement, $this->stmt());
    }

    /**
     * @return Node
     * @throws \Exception
     */
    private function expr(): Node
    {
        return $this->subOrAdd();
    }

    /**
     * @return Node
     * @throws \Exception
     */
    private function term(): Node
    {
        $token = $this->lexer->getToken();

        if ($token['type'] == Lexer::TYPE_FLOAT) {
            $this->lexer->goNext();
            return new ConstNode($token['value']);
        }

        if ($token['type'] == Lexer::TYPE_INT) {
            $this->lexer->goNext();
            return new ConstNode($token['value']);
        }

        if ($token['type'] == Lexer::TYPE_LEFT_ROUND_BRACKET) {
            $this->lexer->goNext();
            $token = $this->lexer->getToken();
            if ($token['type'] == Lexer::TYPE_LEFT_ROUND_BRACKET) {
                $expr = $this->term();
            } else {
                $expr = $this->expr();
            }
            $token2 = $this->lexer->getToken();
            if ($token2['type'] != Lexer::TYPE_RIGHT_ROUND_BRACKET) {
                throw new \Exception("Expected )");
            }
            $this->lexer->goNext();
            return $expr;
        }

        throw new \Exception();
    }

    /**
     * @return Node
     * @throws \Exception
     */
    private function subOrAdd(): Node
    {
        $operand1 = $this->mulOrDiv();
        $operator = $this->lexer->getToken();
        while ($operator['type'] == Lexer::TYPE_OPERATOR && ($operator['value'] == '+' || $operator['value'] == '-')) {
            $this->lexer->goNext();
            $operand1 = new OperatorNode($operator['value'], $operand1, $this->mulOrDiv());
            $operator = $this->lexer->getToken();
        }

        if ($operator['type'] == Lexer::TYPE_OPERATOR) {
            $this->lexer->goNext();
            return new OperatorNode($operator['value'], $operand1, $this->mulOrDiv());
        }

        return $operand1;
    }

    /**
     * @return Node
     * @throws \Exception
     */
    private function mulOrDiv(): Node
    {
        $operand1 = $this->term();
        $operator = $this->lexer->getToken();
        if ($operator['type'] == Lexer::TYPE_OPERATOR && ($operator['value'] == '*' || $operator['value'] == '/')) {
            $operator = $operator['value'];
            $this->lexer->goNext();
            return new OperatorNode($operator, $operand1, $this->term());

        }
        return $operand1;
    }
}