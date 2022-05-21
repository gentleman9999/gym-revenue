module.exports = {
    'resources/**/*.{css,js,svelte}': ['prettier --write'],
    '**/*.php': ['php ./vendor/bin/php-cs-fixer fix --config .php-cs-fixer.php'],
};