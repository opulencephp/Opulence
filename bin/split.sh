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

for prefix in src/Opulence/*/ ;
do
    repo=$(basename "$prefix")
    remote=$(echo "$repo" | awk '{print tolower($0)}')

    echo "Adding remote $remote"
    git remote add "$remote" https://$GIT_USER:$GIT_ACCESS_TOKEN@github.com/opulencephp/$remote.git >/dev/null 2>&1

    echo "Splitting $repo"
    sha=$(./bin/splitsh-lite --prefix="$prefix")

    if [ -z "$sha" ]
    then
        echo "Empty SHA"
        exit 1
    fi

    # Push to the subtree's repo, and do not leak any sensitive info in the logs
    echo "Pushing $repo to $remote"
    git push "$remote" "$sha:refs/heads/$GIT_BRANCH" -f >/dev/null 2>&1
done
