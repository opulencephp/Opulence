#!/usr/bin/env bash

set -e

GIT_REF="$1"

if [ -z "$GIT_REF" ]
then
    echo "No Git ref specified"
    exit 1
fi

declare -A dirs_to_repos=(["Applications"]="applications" ["Authentication"]="authentication" ["Authorization"]="authorization" ["Cache"]="cache" ["Collections"]="collections" ["Console"]="console" ["Cryptography"]="cryptography" ["Databases"]="databases" ["Debug"]="debug" ["Environments"]="environments" ["Events"]="events" ["Files"]="files" ["Framework"]="framework" ["Http"]="http" ["IO"]="io" ["Ioc"]="ioc" ["Memcached"]="memcached" ["Orm"]="orm" ["Pipelines"]="pipelines" ["QueryBuilders"]="querybuilders" ["Redis"]="redis" ["Routing"]="routing" ["Sessions"]="sessions" ["Validation"]="validation" ["Views"]="views")

for dir in "${!dirs_to_repos[@]}"
do
    remote=${dirs_to_repos[$dir]}
    remote_uri="git@github.com:opulencephp/$remote.git"

    if [[ $GIT_REF == "refs/tags/*" ]]; then
        tag_name=${GIT_REF/refs\/tags\//}
        tmp_split_dir="/tmp/tag-split"
        echo "Creating $tmp_split_dir for $remote"
        rm -rf $tmp_split_dir
        mkdir $tmp_split_dir

        (
            echo "Creating $tag_name for $remote"
            cd $tmp_split_dir
            git clone "$remote_uri"
            git checkout "1.2"
            git tag "$tag_name"
            git push origin --tags
        )
    else
        echo "Adding remote $remote"
        git remote add "$remote" "$remote_uri"

        echo "Splitting $dir"
        sha=$(./bin/splitsh-lite --prefix="src/Opulence/$dir")

        if [ -z "$sha" ]
        then
            echo "Empty SHA"
            exit 1
        fi

        echo "Pushing SHA $sha from $dir to $remote"
        git push "$remote" "$sha:$GIT_REF"
    fi
done
