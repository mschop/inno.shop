#!/usr/bin/env bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker build $DIR -t inno_shop_docs --build-arg USER_ID=`id -u` --build-arg GROUP_ID=`id -g`

docker run -v "$DIR:/code" -w '/code' inno_shop_docs mkdocs build
