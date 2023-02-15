<?php

declare(strict_types=1);

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

class SnakeCaseSniff implements Sniff
{
    public const CODE_SNAKE_CASE = 'SnakeCase';
    
    /** @var string[]  */
    public array $ignore = [
        'protected $primaryKey',
        'protected $keyType',
        'protected $routeMiddleware',
        'protected $middlewareGroups',
        'protected $dispatchesEvents',
        'public string $commandSignature',
        'public string $commandDescription',
        'public string $commandHelp',
        'public bool $commandHidden',
        'public string $jobConnection',
        'public string $jobQueue',
        'public int $jobTries',
        'public int $jobMaxExceptions',
        'public int $jobBackoff',
        'public int $jobTimeout',
        'public int $jobRetryUntil',
        'public string $jobUniqueId',
        'public int $jobUniqueFor',
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
        $tokens         = $phpcsFile->getTokens();
        $previous_ptr_1 = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);
        $previous_ptr_2 = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 3);
        $variable       = $tokens[$pointer]['content'];
        $data           = ['variable' => $variable, 'type' => '', 'visibility' => ''];
        $visibilities   = ['public', 'protected', 'private'];
        $types          = ['bool', 'int', 'float', 'string', 'array', 'object', 'callable', 'resource'];


        $type_or_visibility = $accessor = $tokens[$previous_ptr_1]['content'];
        if (in_array($type_or_visibility, $visibilities)) {
            $data['visibility'] = $type_or_visibility;
        } elseif (in_array($type_or_visibility, $types)) {
            $data['type'] = $type_or_visibility;
        }

        $type_or_visibility = $tokens[$previous_ptr_2]['content'];
        if (in_array($type_or_visibility, $visibilities)) {
            $data['visibility'] = $type_or_visibility;
        }

        if ($data['type'] !== '') {
            $variable = "{$data['visibility']} {$data['type']} {$data['variable']}";
        } else {
            $variable = trim("{$data['visibility']} {$data['variable']}");
        }

        if (in_array($variable, $this->ignore) || "{$accessor}{$variable}" === "::{$variable}") {
            return;
        }

        if (preg_match('/((?:^|[A-Z])[a-z]+)/', $variable) > 0) {
            $phpcsFile->addError("{$variable} should be in a snake case format", $pointer, self::CODE_SNAKE_CASE);
        }
    }
}
