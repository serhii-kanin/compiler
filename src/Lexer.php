<?php
namespace SK\Compiler;

//todo refactor!
class Lexer
{
    //todo work on expressions
    private const SEMICOLON_REGEXP = '/^(?<token>;)/';

    private const COMMA_REGEXP = '/^(?<token>,)/';
    private const COLON_REGEXP = '/^(?<token>:)/';

    private const WHILE_EXPR = '/^(?<token>while)(?:\s*\()/i';

    private const FUNCTION_EXPR = '/^(?<token>function)(?:\s+)/i';
    private const IF_REGEXP = '/^(?<token>if)(?:\s*\()/';
    private const ELSE_REGEXP = '/^(?<token>else)(?:\s*\{)/i';
    private const LEFT_CURLY_BRACKET = '/^(?<token>\{)/';
    private const RIGHT_CURLY_BRACKET = '/^(?<token>\})/';
    private const INT_REGEXP = '/^(?<token>[1-9]+[0-9]*)/';
    private const FLOAT_REGEXP = '/^(?<token>[0-9]+\.[0-9]+)/';
    private const STRING_REGEXP = '/^(?<token>\"[^"]*?\")/';
    private const BOOL_REGEXP = '/^(?<token>false|true)(?:\W)/i';
    private const DATA_TYPE_KEYWORD_REGEXP = '/^(?<token>int|float|string|bool)(?:\s+)/i';
    private const VOID_REGEXP = '/^(?<token>void)(?:\s*\{?)/i';
    private const ID_REGEXP = '/^(?<token>[0-9]*[a-z]{1,}[a-z0-9_]*)/i';
    private const OP_REGEXP = '/^(?<token>[\/\*\+\-])/';
    private const SPACE_REGEXP = '/^(?<token>\s+)/';
    private const LEFT_ROUND_BRACKET = '/^(?<token>\()/';
    private const RIGHT_ROUND_BRACKET = '/^(?<token>\))/';
    private const TEST_EQUAL_REGEXP = '/^(?<token>(==|\!=|>=?|=<|<))/';
    private const ASSIGN_REGEXP = '/^(?<token>=)/';
    private const LOGICAL_AND_REGEXP = '/^(?<token>&&)/';
    private const LOGICAL_OR_REGEXP = '/^(?<token>\|\|)/';

    private const REGEXPS = [
        Lexer::FUNCTION_EXPR => Token::TYPE_FUNCTION,
        Lexer::VOID_REGEXP => Token::TYPE_VOID,
        Lexer::STRING_REGEXP => Token::TYPE_STRING,
        Lexer::WHILE_EXPR => Token::TYPE_WHILE,
        Lexer::IF_REGEXP => Token::TYPE_IF,
        Lexer::ELSE_REGEXP => Token::TYPE_ELSE,
        Lexer::BOOL_REGEXP => Token::TYPE_BOOL,
        Lexer::OP_REGEXP => Token::TYPE_OPERATOR,
        Lexer::LEFT_CURLY_BRACKET => Token::TYPE_LEFT_CURLY_BRACKET,
        Lexer::RIGHT_CURLY_BRACKET => Token::TYPE_RIGHT_CURLY_BRACKET,
        Lexer::LEFT_ROUND_BRACKET => Token::TYPE_LEFT_ROUND_BRACKET,
        Lexer::RIGHT_ROUND_BRACKET => Token::TYPE_RIGHT_ROUND_BRACKET,
        Lexer::TEST_EQUAL_REGEXP => Token::TYPE_TEST,
        Lexer::LOGICAL_AND_REGEXP => Token::TYPE_LOGICAL_AND,
        Lexer::LOGICAL_OR_REGEXP => Token::TYPE_LOGICAL_OR,
        Lexer::ASSIGN_REGEXP => Token::TYPE_ASSIGN,
        Lexer::DATA_TYPE_KEYWORD_REGEXP => Token::TYPE_DATA_TYPE_KEYWORD,
        Lexer::ID_REGEXP => Token::TYPE_ID,
        Lexer::FLOAT_REGEXP => Token::TYPE_FLOAT,
        Lexer::INT_REGEXP => Token::TYPE_INT,
        Lexer::SEMICOLON_REGEXP => Token::TYPE_SEMICOLON,
        Lexer::COLON_REGEXP => Token::TYPE_COLON,
        Lexer::COMMA_REGEXP => Token::TYPE_COMMA
    ];

    private $input;

    private $currentToken;

    public function __construct(string $input)
    {
        $this->input = $input;
    }

    public function getToken(): Token
    {
        return $this->currentToken;
    }

    /**
     * @throws \Exception
     */
    public function moveToNext()
    {

        $this->input = trim($this->input);

        if ('' == $this->input) {
            $this->currentToken = new Token(Token::TYPE_EOF);
            return;
        }

        //todo refactor string extracting
        if ($this->input[0] == '"') {
            $n = 1;
            while(true) {
                if ($this->input[$n] == "\"" && $this->input[$n - 1] != "\\") {
                    break;
                }
                $n++;
            }
            $string = substr($this->input, 1, $n - 1);
            $this->currentToken = new Token(Token::TYPE_STRING, ['value' => $string]);
            $this->input = substr($this->input,  mb_strlen($string) + 2);
            return;
        }

        $token = null;
        foreach(Lexer::REGEXPS as $regexp => $tokenName) {
            if (preg_match($regexp, $this->input, $matches)) {
                $token = $matches['token'];
                //echo $tokenName . '->' . $token."\n";
                $this->currentToken = new Token($tokenName, ['value' => $token]);
                $this->input = substr($this->input, mb_strlen($token));
                break;
            }
        }
        if (!$token) {
            throw new \Exception($this->input);
        }
    }

}
