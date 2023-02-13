<?php

declare(strict_types=1);

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

class SnakeCaseSniff implements Sniff
{
    public const CODE_SNAKE_CASE = 'SnakeCase';
    public array $ignore = [
        'protected $primaryKey',
        'protected $keyType',
        'protected $routeMiddleware',
        'protected $middlewareGroups',
        'protected $dispatchesEvents',
    ];

    /**
     * @return array<int, (int|string)>
     */
    public function register(): array
    {
        return [T_VARIABLE, /*...TokenHelper::$propertyModifiersTokenCodes*/];
    }

    public function process(File $phpcsFile, $pointer)
    {
        $tokens = $phpcsFile->getTokens();
        $previous_ptr = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);
        $variable = $tokens[$pointer]['content'];
        $previous_variable = $tokens[$previous_ptr]['content'];
        if (in_array("{$previous_variable} {$variable}", $this->ignore)
            || in_array($variable, $this->ignore)
            || "{$previous_variable} {$variable}" === ":: $variable"
        ) {
            return;
        }

        if (preg_match('/((?:^|[A-Z])[a-z]+)/', $variable) > 0) {
            $phpcsFile->addError("{$variable} should be in a snake case format", $pointer, self::CODE_SNAKE_CASE);
        }
    }
}
