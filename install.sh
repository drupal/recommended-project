#!/bin/bash

source scripts/utils.sh
SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR"

cp ./certificates/example.private.key ./certificates/private.key
cp ./certificates/example.public.key ./certificates/public.key

if [[ "$DDEV" == "true"  ]]; then
  ddev start
  ddev composer install
  ddev exec ./vendor/bin/robo sql:sync
  ddev exec ./vendor/bin/robo site:update
  ddev exec ./vendor/bin/robo site:develop
  ddev launch
else
  composer install
  ./vendor/bin/robo sql:sync
  ./vendor/bin/robo site:update
  ./vendor/bin/robo site:develop
fi