#!/bin/bash

# Get the full path to the directory containing this script.
SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

function args()
{
    options=$(getopt --long ddev -- "$@")
    [ $? -eq 0 ] || {
        echo "Incorrect option provided"
        exit 1
    }
    eval set -- "$options"
    while true; do
        case "$1" in
        --ddev)
            DDEV=true
            ;;
        --)
            shift
            break
            ;;
        esac
        shift
    done
}

DDEV=false

if test -f "$SCRIPT_DIR/../.env"; then
  source .env
fi

args $0 "$@"