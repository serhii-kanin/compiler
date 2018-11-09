<?php
namespace SK\Compiler;


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
     * @throws \Exception
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
