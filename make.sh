#!/usr/bin/env bash

set -eu

CMD=${1:-help}
shift


build () {
	npm install
	npm run build
}

package () {
  # fetch version number from Git or argument
  if [[ -z "$@" ]]; then
    version=$(git describe --tags)-dev
  else
    version=$@
  fi

  date=$(LC_ALL=en_GB.utf8 date +'%B %Y')

  # replace version and date in xml
  sed -e "s/<version>.*<\/version>/<version>${version#v}<\/version>/g" \
    -e "s/<creationDate>.*<\/creationDate>/<creationDate>${date}<\/creationDate>/g" \
    src/mod_einsatz_stats.xml > dist/mod_einsatz_stats.xml

  # move remaining files to dist/
  cp src/helper.php src/mod_einsatz_stats.php dist/
  cp -r src/tmpl dist/
  cd dist/

  zip -r mod-einsatz-stats-${version}.zip *
}

tag-release () {
  if [[ -z "$1" ]]; then
    echo "no version number given"
    exit 0;
  fi
  if ! [[ $1 == v* ]]; then
    echo "version tag needs to start with a 'v'"
    exit 0
  fi

  version=$1
	date=$(LC_ALL=en_GB.utf8 date +'%B %Y')

  # replace version and date in extension.xml
  sed -i -e "s/<version>.*<\/version>/<version>${version#v}<\/version>/g" \
    -e "s/<creationDate>.*<\/creationDate>/<creationDate>${date}<\/creationDate>/g" \
    src/mod_einsatz_stats.xml

  # replace version and release url in update.xml
  sed -i -e "s/<version>.*<\/version>/<version>${version#v}<\/version>/g" \
    -e "s/download\/.*\/mod-einsatz-stats-.*\.zip/download\/${version}\/mod-einsatz-stats-${version#v}\.zip/g" \
    update.xml

  git add src/mod_einsatz_stats.xml update.xml
  git commit -m "release: prepare ${version}"
  git tag "${version}"
}

case $CMD in
  build)      build;;
  package)    package $@;;
  release)    tag-release $@;;
esac
