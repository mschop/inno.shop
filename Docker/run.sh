#!/usr/bin/env bash

set -e

# https://stackoverflow.com/a/246128
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

"$DIR/build.sh"

printf "\n\n❱ Run\n\n"
USER_ID=`id -u` GROUP_ID=`id -g` APP_ENV=${env} docker-compose -f "$DIR/docker-compose.yml" up "$@"
