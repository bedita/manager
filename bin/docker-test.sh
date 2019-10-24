#!/usr/bin/env sh

docker run -p 8090:80 --env BEDITA_ADMIN_USR=bedita --env BEDITA_ADMIN_PWD=bedita bedita/bedita:4.0.0-RC2
