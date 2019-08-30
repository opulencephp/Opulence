#!/usr/bin/env bash

set -e

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

declare -A dirs_to_repos=(["Authentication"]="authentication" ["Authorization"]="authorization" ["Cache"]="cache" ["Collections"]="collections" ["Console"]="console" ["Cryptography"]="cryptography" ["Databases"]="databases" ["Framework"]="framework" ["IO"]="io" ["Ioc"]="ioc" ["Memcached"]="memcached" ["Orm"]="orm" ["QueryBuilders"]="querybuilders" ["Redis"]="redis" ["Sessions"]="sessions" ["Validation"]="validation" ["Views"]="views")

for dir in "${!dirs_to_repos[@]}"
do
    remote=${dirs_to_repos[$dir]}

    echo "Adding remote $remote"
    git remote add "$remote" https://$GIT_USER:$GIT_ACCESS_TOKEN@github.com/opulencephp/$remote.git >/dev/null 2>&1

    echo "Splitting $dir"
    sha=$(./bin/splitsh-lite --prefix="src/Opulence/$dir")

    if [ -z "$sha" ]
    then
        echo "Empty SHA"
        exit 1
    fi

    # Push to the subtree's repo, and do not leak any sensitive info in the logs
    echo "Pushing $dir to $remote"
    git push "$remote" "$sha:refs/heads/$GIT_BRANCH" -f >/dev/null 2>&1
done
