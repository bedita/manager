#!/bin/bash

PATH_LOCALE=./src/Locale

for dir in ${PATH_LOCALE}/*/
do
    dir=${dir%*/}
    l=$(echo ${dir##*/})
    lang=$(echo ${l} | cut -d'_' -f 1)
    node_modules/ttag-cli/bin/ttag extract --o src/Locale/${l}/default-js.po --l ${lang} src/Template/Layout/js/app
    msgcat --use-first src/Locale/${l}/default.po src/Locale/${l}/default-js.po -o src/Locale/${l}/default.po
done
