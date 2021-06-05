#!/usr/bin/env bash

tempdir=dist

if [[ -n "$1" ]]; then
  output=gislercms_${1}.zip
else
  output=gislercms.zip
fi

# prepare dist
[[ -d ${tempdir} ]] && rm -rf ${tempdir}
mkdir ${tempdir}
mkdir ${tempdir}/cache
mkdir ${tempdir}/logs
mkdir ${tempdir}/backups
mkdir ${tempdir}/update
cp -R config ${tempdir}/.
cp -R mysql ${tempdir}/.
cp -R public ${tempdir}/.
cp -R src ${tempdir}/.
cp -R templates ${tempdir}/.
cp -R themes ${tempdir}/.
cp -R translations ${tempdir}/.
cp build_css.sh ${tempdir}/.
cp composer.json ${tempdir}/.
cp composer.lock ${tempdir}/.
cp LICENSE ${tempdir}/.
cp README.md ${tempdir}/.

# remove folders which will be rebuilt with composer
rm -rf ${tempdir}/public/css/webfonts
rm -rf ${tempdir}/public/editor
rm ${tempdir}/config/local.php

# build composer
cd ${tempdir}
composer install
rm composer.json
rm composer.lock
rm build_css.sh

# zip package
zip -r ${output} .
mv ${output} ../.
cd ..
rm -rf ${tempdir}