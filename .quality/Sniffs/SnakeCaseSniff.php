<?php

declare(strict_types=1);

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SnakeCaseSniff implements Sniff
{
    public const CODE_SNAKE_CASE = 'SnakeCase';

    /**
     * @return array<int, (int|string)>
     */
    public function register(): array
    {
        return [T_VARIABLE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $variable_name = $phpcsFile->getTokens()[$stackPtr]['content'];
        if (preg_match('/((?:^|[A-Z])[a-z]+)/', $variable_name) > 0) {
            $phpcsFile->addError("{$variable_name} should be in a snake case format", $stackPtr, self::CODE_SNAKE_CASE);
        }
    }
}
