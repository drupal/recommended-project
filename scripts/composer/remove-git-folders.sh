#!/usr/bin/env sh

find ./vendor -mindepth 2 -type d -name .git | xargs rm -rf
find ./vendor -mindepth 2 -type d -name .github | xargs rm -rf
find ./web -mindepth 2 -type d -name .git | xargs rm -rf
find ./web -mindepth 2 -type d -name .github | xargs rm -rf
find . -mindepth 2 -type f -name .travis.yml | xargs rm -rf
