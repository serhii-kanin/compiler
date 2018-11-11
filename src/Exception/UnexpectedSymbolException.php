<?php
declare(strict_types=1);

namespace SK\Compiler\Exception;

use SK\Compiler\Token;

class UnexpectedSymbolException extends \Exception
{
    public function __construct(Token $token, ?string $expectedMessage = '')
    {
        parent::__construct('Unexpected token '. $token->getType(). ' "'.$token->getValueAttribute().'", '. $expectedMessage, 0, null);
    }
}