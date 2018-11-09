<?php


namespace SK\Compiler;

class Token
{
    private const TYPE_INT = 'INT';
    private const TYPE_FLOAT = 'FLOAT';
    private const TYPE_ID = 'ID';
    private const TYPE_LEFT_ROUND_BRACKET = 'LEFT_ROUND_BRACKET';
    private const TYPE_RIGHT_ROUND_BRACKET = 'RIGHT_ROUND_BRACKET';
    private const TYPE_OPERATOR = 'OPERATOR';
    private const TYPE_EOF = 'EOF';
    private const TYPE_IF = 'IF';
    private const TYPE_ELSE = 'ELSE';
    private const TYPE_LEFT_CURLY_BRACKET = 'LEFT_CURLY_BRACKET';
    private const TYPE_RIGHT_CURLY_BRACKET = 'RIGHT_CURLY_BRACKET';
    private const TYPE_SEMICOLON = 'SEMICOLON';


    private const AVAILABLE_TYPES = [
        Token::TYPE_INT,
        Token::TYPE_FLOAT,
        Token::TYPE_ID,
        Token::TYPE_LEFT_ROUND_BRACKET,
        Token::TYPE_RIGHT_ROUND_BRACKET,
        Token::TYPE_OPERATOR,
        Token::TYPE_EOF,
        Token::TYPE_IF,
        Token::TYPE_ELSE,
        Token::TYPE_LEFT_CURLY_BRACKET,
        Token::TYPE_RIGHT_CURLY_BRACKET,
        Token::TYPE_SEMICOLON
    ];

    /**
     * @var []string
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $type;

    public function __construct(string $type, array $attributes)
    {
        if (!in_array($type, Token::AVAILABLE_TYPES)) {
            throw new \Exception();
        }
        $this->type = $type;
        foreach($this->attributes as $name => $value) {
            $this->addAttribute($name, $value);
        }
    }

    private function addAttribute(string $name, string $value)
    {
        $this->attributes[$name] = $value;
    }

    public function isInt()
    {
        return $this->type == Token::TYPE_INT;
    }

    public function isFloat()
    {
        return $this->type == Token::TYPE_FLOAT;
    }

    public function isId()
    {

    }

    public function isLeftRoundBracket()
    {

    }

    /**
return $this->type == Token::TYPE_ID;
return $this->type == Token::TYPE_LEFT_ROUND_BRACKET;
return $this->type == Token::TYPE_RIGHT_ROUND_BRACKET;
return $this->type == Token::TYPE_OPERATOR;
return $this->type == Token::TYPE_EOF;
return $this->type == Token::TYPE_IF;
return $this->type == Token::TYPE_ELSE;
return $this->type == Token::TYPE_LEFT_CURLY_BRACKET;
return $this->type == Token::TYPE_RIGHT_CURLY_BRACKET;
return $this->type == Token::TYPE_SEMICOLON;
     *
     */
}
