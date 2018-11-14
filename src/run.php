<?php

include "../vendor/autoload.php";

/**
 * function testFunc(): void { int sukaint = !1 + 1; int suka2 = 1;return suka2 + sukaint;}
string stringVar = "12332\"31";
bool boolVar = false;
int false1 = 2;
float float1 = 2.2;
if (1 > baz && 3 < 2 && 1 || true) {
while(4 > 1 && 2 > 1 || 3 < 2) {
if(1 * 2 + 3 != 10 + 3 + 5 * 2) {
bool suka = 1+1;
}
}
} else { bool suka = 1 + 2;}');
 */

$lexer = new \SK\Compiler\Lexer('1 * 2 + 3 * 4 + 5 * 6 + 7 * 8;');


print_r((new \SK\Compiler\Parser($lexer))->parse());


