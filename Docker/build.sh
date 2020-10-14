#!/usr/bin/env bash

set -e

# https://stackoverflow.com/a/246128
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

printf "\n\n❱ Run docker-composer build\n\n"
USER_ID=`id -u` GROUP_ID=`id -g` docker-compose -f "$DIR/docker-compose.yml" build

printf "\n\n❱ Run composer install\n\n"
$DIR/cli/bash.sh composer install --no-interaction --no-progress