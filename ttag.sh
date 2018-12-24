#!/bin/bash

PATH_LOCALE=./src/Locale

for dir in ${PATH_LOCALE}/*/
do
    dir=${dir%*/}
    l=$(echo ${dir##*/})
    lang=$(echo ${l} | cut -d'_' -f 1)
    node_modules/ttag-cli/bin/ttag po2json src/Locale/${l}/default.po > src/Template/Layout/js/app/locales/${l}.json
done