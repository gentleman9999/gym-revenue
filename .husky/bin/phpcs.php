#!/usr/bin/env php
<?php

use PHP_CodeSniffer\Runner;

include_once __DIR__ . '/../../vendor/squizlabs/php_codesniffer/autoload.php';

exec('git diff --cached --name-only --diff-filter=ACMR HEAD | grep .php', $changed_files);
$_SERVER['argv'] = [
    './vendor/bin/phpcbf',
    '--standard=.quality/phpcs.xml',
    '--colors',
    '--encoding=utf-8',
    ...$changed_files
];

$runner    = new Runner();
$exit_code = $runner->runPHPCBF();
if ($exit_code === 0) {
    return;
}

system('git add .');
if ($runner->runPHPCS() === 1) {
    exit(0);
}
