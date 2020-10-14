#!/usr/bin/env bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

USER_ID=`id -u` GROUP_ID=`id -g` docker-compose -f "$DIR/../docker-compose.yml" run --rm -w "/code" cli "$@"