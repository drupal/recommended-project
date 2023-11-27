#!/bin/bash

source scripts/utils.sh

SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR"

preCommand=$([ "$DDEV" == "true" ] && echo "ddev exec" || echo "")

bash -c "$preCommand composer install"
bash -c "$preCommand ./vendor/bin/robo site:update"
