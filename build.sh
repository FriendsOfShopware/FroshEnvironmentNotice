#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag --sort=-creatordate | head -1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf FroshEnvironmentNotice FroshEnvironmentNotice-*.zip

# Build new release
mkdir -p FroshEnvironmentNotice
git archive ${commit} | tar -x -C FroshEnvironmentNotice
composer install --no-dev -n -o -d FroshEnvironmentNotice
cd FroshEnvironmentNotice/Resources/notice-editor
npm install -P
npm run build
cd ..
rm -rf notice-editor
cd ../..
zip -r FroshEnvironmentNotice-${commit}.zip FroshEnvironmentNotice
