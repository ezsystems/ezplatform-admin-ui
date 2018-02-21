#!/usr/bin/env bash

# In order to enable automatic deployment of PRs on Platform.sh follow the instructions at :
# https://docs.platform.sh/administration/integrations/github.html
#
# Create the integration using:
# platform integration:add --type=github --project=PROJECT-ID --token=[GITHUB_TOKEN] --repository=ezsystems/[REPONAME] --build-pull-requests=true --fetch-branches=true
# > Build pull requests based on their post-merge state? N
# > Clone the parent environment's data for pull requests? N

set -e

if [ "$1" == "" ]; then
    echo "Error : Meta repo not set"
    exit 1
fi
if [ "$2" == "" ]; then
    echo "Error : Meta branch not set"
    exit 1
fi
if [ "$3" != "" ]; then
    META_SELF_ALIAS="$3"
fi

METAREPO="$1"
METABRANCH="$2"

curl https://raw.githubusercontent.com/vidarl/platformsh-pr-binaries/master/bin/jq > /tmp/jq
chmod a+x /tmp/jq

# move bundle to /tmp
mkdir /tmp/bundle_repo
shopt -s dotglob
mv * /tmp/bundle_repo/

# Create tmp branch in bundle ( first remove it if a branch with same name by accident already exists )
cd /tmp/bundle_repo
# in platform.sh, it seems like .git dir is gone ....
#git config --global user.email "you@example.com"
#git config --global user.name "Your Name"
#git init
#git add *
#git commit -a -m "foobar"
#git checkout -b tmp_branch

# Put meta in /app
cd /tmp
git clone --depth 1 --single-branch --branch $METABRANCH $METAREPO meta_repo
mv /tmp/meta_repo/* /app/
shopt -u dotglob

cd /app

rm web/app_dev.php
. ./.env

# Next, we need to make sure composer will use our git version of the bundle
cp composer.json composer.json.01 ; cat composer.json.01 |/tmp/jq '.repositories=.repositories + [{"type":"path","url":"/tmp/bundle_repo", "options": { "symlink": false }}]' > composer.json

bundleName=`cat /tmp/bundle_repo/composer.json |/tmp/jq -r '.name'`

# In case we need self-alias
# Fixme : ATM, no way of defining META_SELF_ALIAS in bundle
if [ "$META_SELF_ALIAS" == "" ]; then
    selfAliasString=""
else
    selfAliasString=" as $META_SELF_ALIAS"
fi

composer require --no-update "${bundleName}:dev-master$selfAliasString"

cat composer.json
echo "kake"
grep platform-ui-bundle -r *|grep composer.json
composer install --no-progress --no-interaction --prefer-dist --no-suggest --optimize-autoloader

#composer depends ezsystems/ezplatform-demo

#app/console --env=prod assetic:dump

rm -Rf app/cache/*/* var/cache/*/*
