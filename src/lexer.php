<?php
class Node {

}

class ConstNode extends Node{
    private $value;

    /**
     * ConstNode constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

}

class OperatorNode extends Node
{
    private $operator;
    private $operand1;
    private $operand2;

    public function __construct($operator, Node $operand1, Node $operand2)
    {
        $this->operator = $operator;
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
    }

}



class Lexer
{
    public const TYPE_INT = 'INT';
    public const TYPE_FLOAT = 'FLOAT';
    public const TYPE_ID = 'ID';
    public const TYPE_LEFT_ROUND_BRACKET = 'LEFT_ROUND_BRACKET';
    public const TYPE_RIGHT_ROUND_BRACKET = 'RIGHT_ROUND_BRACKET';
    public const TYPE_OPERATOR = 'OPERATOR';
    public const TYPE_EOF = 'EOF';
    public const TYPE_IF = 'IF';
    public const TYPE_ELSE = 'ELSE';
    public const TYPE_LEFT_CURLY_BRACKET = 'LEFT_CURLY_BRACKET';
    public const TYPE_RIGHT_CURLY_BRACKET = 'RIGHT_CURLY_BRACKET';
    public const TYPE_SEMICOLON = 'SEMICOLON';

    private const SEMICOLON_REGEXP = '/^;/';
    private const IF_REGEXP = '/^if/i';
    private const ELSE_REGEXP = '/^else/i';
    private const LEFT_CURLY_BRACKET = '/^\{/';
    private const RIGHT_CURLY_BRACKET = '/^}/';
    private const INT_REGEXP = '/^[1-9]+[0-9]*/';
    private const FLOAT_REGEXP = '/^[0-9]+\.[0-9]+/';
    private const ID_REGEXP = '/^[0-9]*[a-z]{1,}[0-9]*/i';
    private const OP_REGEXP = '/^[\/\*\+\-]/';
    private const SPACE_REGEXP = '/^\s+/';
    private const LEFT_ROUND_BRACKET = '/^\(/';
    private const RIGHT_ROUND_BRACKET = '/^\)/';

    private $input;

    private $currentToken;

    public function __construct(string $input)
    {
        $this->input = $input;
    }

    public function getToken()
    {
        return $this->currentToken;
    }
    /**
     * @throws Exception
     */
    public function goNext()
    {
        $this->input = trim($this->input);

        if ('' == $this->input) {
            $this->currentToken = [
                'type' => Lexer::TYPE_EOF
            ];
            return;
        }

        $token = null;
        $matches = [];
        if (preg_match(Lexer::ELSE_REGEXP, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_ELSE
            ];
        } elseif (preg_match(Lexer::SEMICOLON_REGEXP, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_SEMICOLON
            ];
        } elseif (preg_match(Lexer::LEFT_CURLY_BRACKET, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_LEFT_CURLY_BRACKET
            ];
        } elseif (preg_match(Lexer::RIGHT_CURLY_BRACKET, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_RIGHT_CURLY_BRACKET
            ];
        } elseif (preg_match(Lexer::IF_REGEXP, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_IF
            ];
        } elseif (preg_match(Lexer::ID_REGEXP, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_ID,
                'value' => $matches[0]
            ];
        } elseif (preg_match(Lexer::FLOAT_REGEXP, $this->input,$matches)) {
            $token = [
                'type' => Lexer::TYPE_FLOAT,
                'value' => $matches[0]
            ];

        } elseif (preg_match(Lexer::INT_REGEXP, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_INT,
                'value' => $matches[0]
            ];
        } elseif(preg_match(Lexer::OP_REGEXP, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_OPERATOR,
                'value' => $matches[0]
            ];
        } elseif(preg_match(Lexer::LEFT_ROUND_BRACKET, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_LEFT_ROUND_BRACKET
            ];
        } elseif(preg_match(Lexer::RIGHT_ROUND_BRACKET, $this->input, $matches)) {
            $token = [
                'type' => Lexer::TYPE_RIGHT_ROUND_BRACKET
            ];
        }

        //var_dump($this->input);
        //print_r($token);
        if (!$token) {
            throw new \Exception($this->input);
        }
        $this->input = substr($this->input, mb_strlen($matches[0]));

        $this->currentToken = $token;
    }

}

class Parser
{
    private $lexer;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->lexer->goNext();
    }

    public function parse()
    {
        print_r($this->expr());
    }

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

    private function stmt(): Node
    {
        $this->expr();
        $this->lexer->goNext();

    }

    private function expr(): Node
    {
        return $this->subOrAdd();
    }

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

$lexer = new Lexer("(10 - 1.2 + 111) * (2 + 333) * 222;");

(new Parser($lexer))->parse();

/**


stmt -> if <enclosed_expr> { <stmt> } | if <enclosed_expr> { <stmt> } else { <stmt> } | while <enclosed_expr> { <stmt> } | <expr>;



var_declaration -> <id>=<expr>;

enclosed_expr -> (<expr>)

expr -> sub_and_add

sub_and_add -> <mul_and_div> - <mul_and_div> | <mul_and_div> + <mul_and_div> | <mul_and_div>

mul_and_div -> <term> * <term> | <term> / <term> | <term>

term -> <digit> | <id> | <enclosed_expr>

id -> [0-9]*[a-zA-Z_]+[a-zA-Z0-9_]*
digit -> int | float

int -> [1-9]+[0-9]*

float -> [1-9]+([0-9])*\.[0-9]+
 */

