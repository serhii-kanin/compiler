<?php

include "../vendor/autoload.php";



$lexer = new \SK\Compiler\Lexer("(10 - 1.2 + 111) * (2 + 333) * 222;1+2; if (1) { 1+1 }");

(new \SK\Compiler\Parser($lexer))->parse();

/**


stmt -> if <enclosed_expr> { <stmt> } | if <enclosed_expr> { <stmt> } else { <stmt> } | while <enclosed_expr> { <stmt> } | <expr>; | <stmt>;



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

