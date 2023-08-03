#!/bin/bash

# Run code quality scripts.
# @see https://github.com/eaudeweb/drupal-code-qa-action

# Get the full path to the directory containing this script.
SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR"

# PHPLint
./vendor/bin/phplint --no-cache --no-progress --extensions=php --extensions=module --extensions=inc --extensions=install --extensions=test --extensions=theme ./web/modules/custom/
./vendor/bin/phplint --no-cache --no-progress --extensions=php --extensions=module --extensions=inc --extensions=install --extensions=test --extensions=theme ./web/themes/custom/

# PHPMD
curl https://raw.githubusercontent.com/eaudeweb/drupal-code-qa-action/2.x/phpmd.xml > phpmd.xml
./vendor/bin/phpmd ./web/modules/custom/ github phpmd.xml

# PHPCS
./vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,theme,test,info,yml ./web/modules/custom/
./vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,theme,test,info,yml --ignore=node_modules ./web/themes/custom/

# PHPStan
./vendor/bin/phpstan analyze --level 9 web/modules/custom/

# PHP Code Beautifier and Fixes
./vendor/bin/phpcbf --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,test,info,yml ./web/modules/custom/
./vendor/bin/phpcbf --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,theme,test,info,yml --ignore=node_modules ./web/themes/custom/