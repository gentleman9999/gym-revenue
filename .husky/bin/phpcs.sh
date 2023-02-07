#!/bin/sh

#########################
#                       #
#     Initializing      #
#                       #
#########################

##
# You should add all tools as composer dependencies or change this path
#
# composer require phpmd/phpmd
# composer require squizlabs/php_codesniffer
##

PHPCS_BIN=./vendor/bin/phpcs
PHPCBF_BIN=./vendor/bin/phpcbf

# Check for PHPCS / PHPCBF
if [ ! -x $PHPCS_BIN ]; then
    echo "[PRE-COMMIT] PHP CodeSniffer is not installed locally."
    echo "[PRE-COMMIT] Please run 'composer install' or check the path: $PHPCS_BIN"
    exit 1
fi

if [ ! -x $PHPCBF_BIN ]; then
    echo "[PRE-COMMIT] PHP Code Beautifier and Fixer is not installed locally."
    echo "[PRE-COMMIT] Please run 'composer install' or check the path: $PHPCBF_BIN"
    exit 1
fi


#########################
#                       #
#       Starting        #
#                       #
#########################

# All files in staging area (Added | Created | Modified | Renamed)

FILES=$(git diff --cached --name-only --diff-filter=ACMR HEAD | grep .php)

if [ "$FILES" != "" ]
then
    # Coding Standards
    echo "[PRE-COMMIT] Checking PHPCS..."

    # You can change your PHPCS command here
    $PHPCS_BIN --standard=.quality/phpcs.xml -n $FILES

#    if [ $? != 0 ]
#    then
#        echo "[PRE-COMMIT] Coding standards errors have been detected."
#        echo "[PRE-COMMIT] Running PHP Code Beautifier and Fixer..."
#
#        # You can change your PHPCBF command here
##        $PHPCBF_BIN -n $FILES
#
#        echo "[PRE-COMMIT] Checking PHPCS again..."
#
#        # You can change your PHPCS command here
#        $PHPCS_BIN -n $FILES
#
#        if [ $? != 0 ]
#        then
#            echo "[PRE-COMMIT] PHP Code Beautifier and Fixer wasn't able to solve all problems."
#            echo "[PRE-COMMIT] Run PHPCS manually to check and fix all errors."
#            exit 1
#        fi
#
#        echo "[PRE-COMMIT] All errors are fixed automatically."
#
#        git add $FILES
#    else
#        echo "[PRE-COMMIT] No errors found."
#    fi
fi

exit $?
