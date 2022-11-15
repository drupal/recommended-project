#!/bin/bash

# Run code quality scripts.
# @see https://github.com/eaudeweb/drupal-code-qa-action

# Get the full path to the directory containing this script.
SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR"

./vendor/bin/phplint --no-cache --no-progress --extensions=php,module,inc,install,test,theme ./web/themes/custom/ ./web/modules/custom/
./vendor/bin/phpmd  ./web/modules/custom/ ansi phpmd.xml
./vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,test,info,yml ./web/modules/custom/
./vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,theme,test,info,yml --ignore=node_modules ./web/themes/custom/
