REPOS=(applications authentication configs cryptography databases exceptions files http ioc orm routing sessions users views)
SUBTREE_DIR="app/rdev"

for repo in ${REPOS[@]}
do
    git rm -r $SUBTREE_DIR/$repo
done

git commit -am "Removed subtrees in attempt to fix them"

for repo in ${REPOS[@]}
do
    git subtree add --prefix=$SUBTREE_DIR/$repo --squash $repo master
done

exit 0

function commit()
{
    # Check if we need to commit RDev
    if ! git diff --quiet ; then
        read -p "   Commit message: " message

        git add .
        git commit -m "$message"
        git push origin master
    fi

    ## Check if we need to commit components
    for repo in ${REPOS[@]}
    do
        if git diff --quiet $repo/master master:$SUBTREE_DIR/$repo; then
            echo "   No changes in $repo"
        else
            echo "   Pushing $repo"
            git subtree push --prefix=$SUBTREE_DIR/$repo --squash $repo master
        fi
    done
}

function tag()
{
    read -p "   Tag Name: " tagname
    read -p "   Commit message: " message

    # Tag RDev
    git tag -a $tagname -m "$message"
    git push origin $tagname

    # Tag components
    for repo in ${repos[@]}
    do
        cd ../$repo
        echo "   Pulling $repo"
        git pull origin master
        echo "   Tagging $repo"
        git tag -a $tagname -m  "$message"
        git push origin $tagname
    done

    cd ../rdev
}

while true; do
    # Display options
    echo "   Select an action"
    echo "   c: Commit"
    echo "   t: Tag"
    echo "   e: Exit"
    read -p "   Choice: " choice

    case $choice in
        [cC]* ) commit;;
        [tT]* ) tag;;
        [eE]* ) exit 0;;
        * ) echo "   Invalid choice";;
    esac
done