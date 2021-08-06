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
  if [[ -z "$@" ]]; then
    echo "no version number given"
    exit 0;
  fi

	# TODO
}

case $CMD in
  build)      build;;
  package)    package $@;;
  relase)     tag-release $@;;
esac
