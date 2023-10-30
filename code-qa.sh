#!/bin/bash

# Run code quality scripts.
# @see https://github.com/eaudeweb/drupal-code-qa-action

source scripts/utils.sh
SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR"

preCommand=$([ "$DDEV" == "true" ] && echo "ddev exec" || echo "")

# PHPLint
echo "Running PHPLint..."
bash -c "$preCommand ./vendor/bin/phplint --no-cache --no-progress --extensions=php --extensions=module --extensions=inc --extensions=install --extensions=test --extensions=theme ./web/modules/custom/"
bash -c "$preCommand ./vendor/bin/phplint --no-cache --no-progress --extensions=php --extensions=module --extensions=inc --extensions=install --extensions=test --extensions=theme ./web/themes/custom/"

# PHPMD
echo "Running PHPMD..."
curl https://raw.githubusercontent.com/eaudeweb/drupal-code-qa-action/2.x/phpmd.xml > phpmd.xml
bash -c "$preCommand ./vendor/bin/phpmd ./web/modules/custom/ github phpmd.xml"

# PHPCS
echo "Running PHPCS..."
bash -c "$preCommand ./vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,theme,test,info,yml ./web/modules/custom/"
bash -c "$preCommand ./vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,theme,test,info,yml --ignore=node_modules ./web/themes/custom/"

# PHPStan
echo "Running PHPStan..."
curl https://raw.githubusercontent.com/eaudeweb/drupal-code-qa-action/2.x/phpstan.neon > phpstan.neon
bash -c "$preCommand ./vendor/bin/phpstan analyze --level=5"

# PHP Code Beautifier and Fixes
echo "Running PHPCBF..."
bash -c "$preCommand ./vendor/bin/phpcbf --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,test,info,yml ./web/modules/custom/"
bash -c "$preCommand ./vendor/bin/phpcbf --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,profile,theme,test,info,yml --ignore=node_modules ./web/themes/custom/"
