#!/usr/bin/env php
<?php

use PHP_CodeSniffer\Runner;

include_once __DIR__ . '/../../vendor/squizlabs/php_codesniffer/autoload.php';

exec('git diff --cached --name-only --diff-filter=ACMR HEAD | grep .php', $changed_files);
if (!empty($changed_files)) {
    return;
}

$_SERVER['argv'] = [
    './vendor/bin/phpcs',
    '--standard=.quality/phpcs.xml',
    ...$changed_files
];

$runner    = new Runner();
$exit_code = $runner->runPHPCBF();
system('git add .');
exit($runner->runPHPCS());
