#!/usr/bin/env bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker build $DIR -t inno_shop_docs --build-arg USER_ID=`id -u` --build-arg GROUP_ID=`id -g`

docker run -v "$DIR:/code" -w '/code' -p 8080:8080 inno_shop_docs mkdocs serve -a 0.0.0.0:8080
