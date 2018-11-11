<?php


namespace SK\Compiler;

class Token
{
    public const TYPE_INT = 'INT';
    public const TYPE_FLOAT = 'FLOAT';
    public const TYPE_BOOL = 'BOOL';
    public const TYPE_STRING = 'STRING';
    public const TYPE_ID = 'ID';
    public const TYPE_FUNCTION = 'FUNCTION';
    public const TYPE_LEFT_ROUND_BRACKET = 'LEFT_ROUND_BRACKET';
    public const TYPE_RIGHT_ROUND_BRACKET = 'RIGHT_ROUND_BRACKET';
    public const TYPE_OPERATOR = 'OPERATOR';
    public const TYPE_EOF = 'EOF';
    public const TYPE_IF = 'IF';
    public const TYPE_ELSE = 'ELSE';
    public const TYPE_LEFT_CURLY_BRACKET = 'LEFT_CURLY_BRACKET';
    public const TYPE_RIGHT_CURLY_BRACKET = 'RIGHT_CURLY_BRACKET';
    public const TYPE_SEMICOLON = 'SEMICOLON';
    public const TYPE_COLON = 'COLON';
    public const TYPE_WHILE = 'WHILE';
    public const TYPE_TEST = 'TEST';
    public const TYPE_LOGICAL_AND = 'LOGICAL_AND';
    public const TYPE_LOGICAL_OR = 'LOGICAL_OR';
    public const TYPE_ASSIGN = 'ASSIGN';
    public const TYPE_DATA_TYPE_KEYWORD = 'DATA_TYPE_KEYWORD';
    public const TYPE_COMMA = 'COMMA';
    public const TYPE_VOID = 'VOID';
    public const TYPE_RETURN = 'RETURN';

    private const AVAILABLE_TYPES = [
        Token::TYPE_DATA_TYPE_KEYWORD,
        Token::TYPE_ASSIGN,
        Token::TYPE_TEST,
        Token::TYPE_FUNCTION,
        Token::TYPE_LOGICAL_AND,
        Token::TYPE_LOGICAL_OR,
        Token::TYPE_WHILE,
        Token::TYPE_BOOL,
        Token::TYPE_INT,
        Token::TYPE_STRING,
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
        Token::TYPE_SEMICOLON,
        Token::TYPE_COLON,
        Token::TYPE_COMMA,
        Token::TYPE_VOID,
        Token::TYPE_RETURN
    ];

    /**
     * @var []string
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $type;


    public function __construct(string $type, array $attributes = [])
    {
        if (!in_array($type, Token::AVAILABLE_TYPES)) {
            throw new \Exception();
        }
        $this->type = $type;
        foreach($attributes as $name => $value) {
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

    public function isBool()
    {
        return $this->type == Token::TYPE_BOOL;
    }

    public function isString()
    {
        return $this->type == Token::TYPE_STRING;
    }

    public function isId()
    {
        return $this->type == Token::TYPE_ID;
    }

    public function isLeftRoundBracket()
    {
        return $this->type == Token::TYPE_LEFT_ROUND_BRACKET;
    }

    public function isRightRoundBracket()
    {
        return $this->type == Token::TYPE_RIGHT_ROUND_BRACKET;
    }

    public function isOperator()
    {
        return $this->type == Token::TYPE_OPERATOR;
    }

    public function isIf()
    {
        return $this->type == Token::TYPE_IF;
    }

    public function isElse()
    {
        return $this->type == Token::TYPE_ELSE;
    }

    public function isEOF()
    {
        return $this->type == Token::TYPE_EOF;
    }

    public function isLeftCurlyBracket()
    {
        return $this->type == Token::TYPE_LEFT_CURLY_BRACKET;
    }

    public function isColon()
    {
        return $this->type == Token::TYPE_COLON;
    }
    public function isRightCurlyBracket()
    {
        return $this->type == Token::TYPE_RIGHT_CURLY_BRACKET;
    }

    public function isSemicolon()
    {
        return $this->type == Token::TYPE_SEMICOLON;
    }

    public function isWhile()
    {
        return $this->type == Token::TYPE_WHILE;
    }

    public function isTest()
    {
        return $this->type == Token::TYPE_TEST;
    }

    public function isLogicalAnd()
    {
        return $this->type == Token::TYPE_LOGICAL_AND;
    }

    public function isLogicalOr()
    {
        return $this->type == Token::TYPE_LOGICAL_OR;

    }

    public function isDataTypeKeyword()
    {
        return $this->type == Token::TYPE_DATA_TYPE_KEYWORD;
    }

    public function isVoid()
    {
        return $this->type == Token::TYPE_VOID;
    }

    public function isAssign()
    {
        return $this->type == Token::TYPE_ASSIGN;
    }

    public function isFunction()
    {
        return $this->type == Token::TYPE_FUNCTION;
    }

    public function isComma()
    {
        return $this->type == Token::TYPE_COMMA;
    }

    public function isReturn()
    {
        return $this->type == Token::TYPE_RETURN;
    }

    public function getValueAttribute(): ?string
    {
        return isset($this->attributes['value']) ? $this->attributes['value']: null;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
