<?php

include "../vendor/autoload.php";



$lexer = new \SK\Compiler\Lexer('
function testFunc(): void { int sukaint = 1 + 1;}
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

print_r((new \SK\Compiler\Parser($lexer))->parse());

