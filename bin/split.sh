#!/usr/bin/env bash

GIT_BRANCH="$1"
GIT_USER="$2"
GIT_ACCESS_TOKEN="$3"

if [ -z "$GIT_BRANCH" ]
then
    echo "No Git branch specified"
    exit 1
fi

if [ -z "$GIT_USER" ]
then
    echo "No Git user specified"
    exit 1
fi

if [ -z "$GIT_ACCESS_TOKEN" ]
then
    echo "No Git access token specified"
    exit 1
fi

REPOS=(Authentication Authorization Cache Collections Console Cryptography Databases Debug Environments Events Framework Http IO Ioc Memcached Orm Pipelines QueryBuilders Redis Routing Sessions Validation Views)

function split()
{
    prefix=$1
    remote=$2
    echo "Splitting $prefix"
    sha=$(./bin/splitsh-lite --prefix="$prefix")

    if [ -z "$sha" ]
    then
        echo "Empty SHA"
        exit 1
    fi

    # Push to the subtree's repo, and do not leak any sensitive info in the logs
    echo "Pushing $prefix to $remote"
    git push "$remote" "$sha:refs/heads/$GIT_BRANCH" -f #>/dev/null 2>&1
}

for repo in ${REPOS[@]}
do
    lower_repo=$(echo "$repo" | awk '{print tolower($0)}')
    git remote add "$lower_repo" https://$GIT_USER:$GIT_ACCESS_TOKEN@github.com/opulencephp/$lower_repo.git >/dev/null 2>&1
    split "src/Opulence/$repo" "$lower_repo"
done
