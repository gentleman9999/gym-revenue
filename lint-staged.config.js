module.exports = {
    "resources/**/*.{css,js,vue,gql}": ["prettier --write"],
    "**/*.php": [
        "php ./vendor/bin/php-cs-fixer fix --config .php-cs-fixer.php",
    ],
};
