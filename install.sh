#!/bin/bash

# Get the full path to the directory containing this script.
SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR"

composer install

./vendor/bin/robo sql:sync
./vendor/bin/robo site:update
./vendor/bin/robo site:develop
