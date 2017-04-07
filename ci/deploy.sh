#!/bin/bash
if [ $1 == "staging" ]; then
    server="deploy@staging.et.tc"
    folder="/var/www/et.tc/Staging"
else
    echo "Not implemented, yet"
    exit
fi

# install ssh key
eval $(ssh-agent -s)
ssh-add <(echo "$SSH_PRIVATE_KEY")

# get current commit
commit=$(git rev-parse HEAD)

# ssh to server
ssh -T $server << EOSSH

cd $folder

# update git
git fetch
git checkout $commit

# upgrade packages
composer install

# todo: migrate database

# generate documentation
apidoc -i src/ -o public/apidoc/
vendor/phpdocumentor/phpdocumentor/bin/phpdoc -d src/ -t public/docs/

EOSSH
