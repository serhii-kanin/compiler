<?php

namespace SK\Compiler;

use SK\Compiler\Exception\UnexpectedSymbolException;
use SK\Compiler\Node\ComparatorNode;
use SK\Compiler\Node\ConstNode;
use SK\Compiler\Node\ConditionNode;
use SK\Compiler\Node\FunctionArgumentNode;
use SK\Compiler\Node\FunctionDeclarationNode;
use SK\Compiler\Node\InversionNode;
use SK\Compiler\Node\ReturnNode;
use SK\Compiler\Node\LogicalOperatorNode;
use SK\Compiler\Node\Node;
use SK\Compiler\Node\NullNode;
use SK\Compiler\Node\MathOperatorNode;
use SK\Compiler\Node\StatementNode;
use SK\Compiler\Node\VarDeclarationNode;
use SK\Compiler\Node\VariableNode;
use SK\Compiler\Node\WhileNode;

//todo add syntax checks like ; after a var declaration...
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
        $this->lexer->moveToNext();
    }

    /**
     * @throws \Exception
     */
    public function parse(): Node
    {
        return $this->statement();
    }


    /**
     * @param bool $inCurlyBrackets
     * @return Node
     * @throws \Exception
     */
    private function statement(bool $inCurlyBrackets = false): Node
    {
        if ($this->lexer->getToken()->isEOF()) {
            return new NullNode();
        }

        if ($this->lexer->getToken()->isIf()) {
            $statement = $this->condition();
        } elseif($this->lexer->getToken()->isFunction()) {
            /**
             * Нельзя создавать функции в { }
             */
            if ($inCurlyBrackets) {
                throw new UnexpectedSymbolException($this->lexer->getToken());
            }
            $this->lexer->moveToNext();
            $statement = $this->functionDeclaration();
        } elseif ($this->lexer->getToken()->isReturn()) {
            $this->lexer->moveToNext();
            $statement = new ReturnNode($this->expression());
            if (!$this->lexer->getToken()->isSemicolon()) {
                throw new UnexpectedSymbolException($this->lexer->getToken(), '; expected');
            }
            $this->lexer->moveToNext();

        } elseif($this->lexer->getToken()->isWhile()) {
            $statement = $this->while();
        } elseif($this->lexer->getToken()->isDataTypeKeyword()) {
            $type = $this->lexer->getToken()->getValueAttribute();
            $this->lexer->moveToNext();
            $statement = $this->varDeclaration($type);
        }
        else {
            $statement = $this->expression();
            if (!$this->lexer->getToken()->isSemicolon()) {
                throw new UnexpectedSymbolException($this->lexer->getToken());
            }
            $this->lexer->moveToNext();
        }

        /**
         * Если это выражение заключено в фигурные скобки и след токен = } тогда это конец выражения
         */
        if ($inCurlyBrackets && $this->lexer->getToken()->isRightCurlyBracket()) {
            return $statement;
        }

        return new StatementNode($statement, $this->statement($inCurlyBrackets));
    }

    /**
     * @return FunctionDeclarationNode
     * @throws UnexpectedSymbolException
     * @throws \Exception
     */
    private function functionDeclaration(): FunctionDeclarationNode
    {
        if (!$this->lexer->getToken()->isId()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(), 'function name expected');
        }

        $functionName = $this->lexer->getToken();


        $this->lexer->moveToNext();

        if (!$this->lexer->getToken()->isLeftRoundBracket()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(), '( expected after function name');
        }

        $this->lexer->moveToNext();

        $argumentList = [];
        while(!$this->lexer->getToken()->isRightRoundBracket()) {
            $type = $this->lexer->getToken();
            if (!$type->isDataTypeKeyword()) {
                throw new UnexpectedSymbolException($type, 'data type expected in function args');
            }
            $this->lexer->moveToNext();
            $id = $this->lexer->getToken();

            if (!$id->isId()) {
                throw new UnexpectedSymbolException($id, 'argument name expected in function args');
            }
            $argumentList[] = new FunctionArgumentNode($type, $id);
            $this->lexer->moveToNext();
            if ($this->lexer->getToken()->isComma()) {
                $this->lexer->moveToNext();
            }
        }

        $this->lexer->moveToNext();

        if (!$this->lexer->getToken()->isColon()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(), ': expected after function header');
        }

        $this->lexer->moveToNext();

        if (!$this->lexer->getToken()->isDataTypeKeyword() && !$this->lexer->getToken()->isVoid()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(), 'return type expected after function header');
        }

        $returnType = $this->lexer->getToken();

        $this->lexer->moveToNext();

        if (!$this->lexer->getToken()->isLeftCurlyBracket()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(), '{ expected after function return type');
        }

        $this->lexer->moveToNext();

        if ($this->lexer->getToken()->isRightCurlyBracket()) {
            $functionBody = new NullNode();
        } else {
            $functionBody = $this->statement(true);
        }

        if (!$this->lexer->getToken()->isRightCurlyBracket()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(), '} expected after function body');
        }

        $this->lexer->moveToNext();

        return new FunctionDeclarationNode(
            $returnType->getValueAttribute(),
            $functionName->getValueAttribute(), $argumentList, $functionBody
        );
    }

    /**
     * @param string $type
     * @return Node
     * @throws UnexpectedSymbolException
     * @throws \Exception
     */
    private function varDeclaration(string $type): Node
    {
        $id = $this->lexer->getToken();
        $expr = null;
        $this->lexer->moveToNext();

        if ($this->lexer->getToken()->isAssign()) {
            $this->lexer->moveToNext();
            $expr = $this->expression();
        }

        if (!$this->lexer->getToken()->isSemicolon()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(), '; expected');
        }
        $this->lexer->moveToNext();

        return new VarDeclarationNode($type, new VariableNode($id->getValueAttribute()), $expr);
    }


    /**
     * if (<expr>) {<stmt>} [else {<stmt>}]
     *
     * @return Node
     * @throws \Exception
     */
    private function condition(): Node
    {
        $this->lexer->moveToNext();
        if (!$this->lexer->getToken()->isLeftRoundBracket()) {
            throw new \Exception('( expected');
        }

        $this->lexer->moveToNext();
        $expression = $this->expression();
        if (!$this->lexer->getToken()->isRightRoundBracket()) {
            throw new \Exception(') expected');
        }

        $this->lexer->moveToNext();

        $trueStatement = $this->stmtInCurlyBrackets();

        $elseStatement = new NullNode();
        if ($this->lexer->getToken()->isElse()) {
            $this->lexer->moveToNext();
            $elseStatement = $this->stmtInCurlyBrackets();
        }
        return new ConditionNode($expression, $trueStatement, $elseStatement);
    }
    /**
     * while (<expr>) { <stmt> }
     *
     * @return Node
     * @throws UnexpectedSymbolException
     * @throws \Exception
     */
    private function while(): Node
    {
        $this->lexer->moveToNext();
        if (!$this->lexer->getToken()->isLeftRoundBracket()) {
            throw new \Exception('( expected');
        }

        $this->lexer->moveToNext();
        $headExpr = $this->expression();
        if (!$this->lexer->getToken()->isRightRoundBracket()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(),') expected');
        }

        $this->lexer->moveToNext();

        return new WhileNode($headExpr, $this->stmtInCurlyBrackets());
    }

    /**
     * { <expr> }
     *
     * @return Node
     * @throws \Exception
     */
    private function stmtInCurlyBrackets(): Node
    {
        if (!$this->lexer->getToken()->isLeftCurlyBracket()) {
            throw new \Exception('{ excepted');
        }
        $this->lexer->moveToNext();
        $statement = $this->statement(true);

        if (!$this->lexer->getToken()->isRightCurlyBracket()) {
            throw new UnexpectedSymbolException($this->lexer->getToken(),'} excepted');
        }
        $this->lexer->moveToNext();

        return $statement;
    }

    /**
     * <logical_end>
     *
     * @return Node
     * @throws \Exception
     */
    private function expression(): Node
    {
        return $this->logicalOrExpression();
    }

    /**
     * <logical_and> && <inversion_or_test> | <inversion_or_test>
     * @return Node
     * @throws \Exception
     */
    private function logicalAndExpression(): Node
    {
        $expr = $this->testExpression();
        while (($token = $this->lexer->getToken()) && $token->isLogicalAnd()) {
            $this->lexer->moveToNext();
            $expr = new LogicalOperatorNode($token->getValueAttribute(), $expr, $this->testExpression());
        }

        return $expr;
    }

    /**
     * <logical_or> || <logical_and> | <logical_and>
     *
     * @return Node
     * @throws \Exception
     */
    public function logicalOrExpression(): Node
    {
        $expr = $this->logicalAndExpression();

        while (($token = $this->lexer->getToken()) && $token->isLogicalOr()) {
            $this->lexer->moveToNext();
            $expr = new LogicalOperatorNode($token->getValueAttribute(), $expr, $this->logicalAndExpression());
        }

        return $expr;
    }

    /**
     * <sub_and_add> > <sub_and_add> |
     * <sub_and_add> < <sub_and_add> |
     * <sub_and_add> == <sub_and_add> |
     * <sub_and_add> != <sub_and_add> |
     * <sub_and_add>
     *
     * @return Node
     * @throws \Exception
     */
    private function testExpression(): Node
    {
        $operand1 = $this->mathAddOrSub();

        if ($this->lexer->getToken()->isTest()) {
            $type = $this->lexer->getToken()->getValueAttribute();
            $this->lexer->moveToNext();
            return new ComparatorNode($type, $operand1, $this->mathAddOrSub());
        }

        return $operand1;
    }

    /**
     * @return Node
     * @throws \Exception
     */
    private function term(): Node
    {
        if ($this->lexer->getToken()->isInversion()) {
            $this->lexer->moveToNext();
            return new InversionNode($this->value());
        }
        return $this->value();
    }
    /**
     * @return Node
     * @throws \Exception
     */
    private function value(): Node
    {
        $token = $this->lexer->getToken();

        if ($token->isFloat()) {
            $this->lexer->moveToNext();
            return new ConstNode($token->getType(), $token->getValueAttribute());
        }

        if ($token->isInt()) {
            $this->lexer->moveToNext();
            return new ConstNode($token->getType(), $token->getValueAttribute());
        }

        if ($token->isBool()) {
            $this->lexer->moveToNext();
            return new ConstNode($token->getType(), $token->getValueAttribute());
        }

        if ($token->isString()) {
            $this->lexer->moveToNext();
            return new ConstNode($token->getType(), $token->getValueAttribute());
        }

        if ($token->isId()) {
            $this->lexer->moveToNext();
            return new VariableNode($token->getValueAttribute());
        }

        if ($token->isLeftRoundBracket()) {
            $this->lexer->moveToNext();
            $token = $this->lexer->getToken();
            if ($token->isLeftRoundBracket()) {
                $expr = $this->term();
            } else {
                $expr = $this->expression();
            }
            $token2 = $this->lexer->getToken();
            if (!$token2->isRightRoundBracket()) {
                throw new UnexpectedSymbolException($token2,") expected");
            }
            $this->lexer->moveToNext();
            return $expr;
        }

        throw new UnexpectedSymbolException($token);
    }

    /**
     * @return Node
     * @throws \Exception
     */
    private function mathAddOrSub(): Node
    {
        $operand1 = $this->mathMulOrDiv();
        $operator = $this->lexer->getToken();
        while ($operator->isOperator() && ($operator->getValueAttribute() == '+' || $operator->getValueAttribute() == '-')) {
            $this->lexer->moveToNext();
            $operand1 = new MathOperatorNode($operator->getValueAttribute(), $operand1, $this->mathMulOrDiv());
            $operator = $this->lexer->getToken();
        }

        return $operand1;
    }

    /**
     * @return Node
     * @throws \Exception
     */
    private function mathMulOrDiv(): Node
    {
        $operand1 = $this->term();
        $operator = $this->lexer->getToken();
        if ($operator->isOperator() && ($operator->getValueAttribute() == '*' || $operator->getValueAttribute() == '/')) {
            $operator = $operator->getValueAttribute();
            $this->lexer->moveToNext();
            return new MathOperatorNode($operator, $operand1, $this->term());

        }
        return $operand1;
    }
}
/**



stmt -> if <enclosed_expr> { <stmt> }
| if <enclosed_expr> { <stmt> } else { <stmt> } | while <enclosed_expr> { <stmt> } | <var_declaration> | <expr>; | <stmt>
| <function_declaration>
| return <expr>;

function_arguments -> <data_type_keyword> <id>, <function_arguments> | <data_type_keyword> <id>
return_type_keyword -> <data_type_keyword> | void
function_declaration -> function <id> (<function_arguments>): <return_type_keyword> { <stmt> }

enclosed_expr -> (<expr>)

expr -> <logical_and>

var_declaration -> <data_type_keyword> <id>; | <data_type_keyword> <id> = <expr>;

data_type_keyword -> int | float | string | bool
logical_or -> <logical_or> || <logical_and> | <logical_and>
logical_and -> <logical_and> && <test> | <test>

test -> <sub_and_add> > <sub_and_add> | <sub_and_add> < <sub_and_add> | <sub_and_add> == <sub_and_add> | <sub_and_add> != <sub_and_add> | <sub_and_add>

sub_and_add -> <mul_and_div> - <mul_and_div> | <mul_and_div> + <mul_and_div> | <mul_and_div>

mul_and_div -> <term> * <term> | <term> / <term> | <term>

term -> <inverse_term> | <value>
inverse_term -> !<value> | <value>

value -> <primitive_data_type> | <id> | <enclosed_expr>
primitive_data_type -> <int> | <float> | <bool> | <string>

string -> "[^"]*"
id -> [0-9]*[a-zA-Z_]+[a-zA-Z0-9_]*

bool -> true | false

int -> [1-9]+[0-9]*

float -> [1-9]+([0-9])*\.[0-9]+

 */

/**
OOP

class_declaration -> class <id> { <class_body> }
| class <id> extends <id> { <class_body> }
| class <id> extends <id> implements <interface_list> { <class_body> }

interface_list -> <id> | <id>, <interface_list>

class_body -> <class_var_declaration> | <class_method_declaration> | <class_body>

class_var_declaration -> <access_modifier> <data_type_keyword> <id>; |
<access_modifier> <data_type_keyword> <id> = <primitive_data_type>;

class_method_declaration -> <access_modifier> <function_declaration>

access_modifier -> public | private | protected
 */