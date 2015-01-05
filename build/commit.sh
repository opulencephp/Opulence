REPOS=(applications authentication configs console cryptography databases files http ioc orm routing sessions users views)
SUBTREE_DIR="app/rdev"

function commit()
{
    # Check if we need to commit RDev
    if ! git diff --quiet ; then
        read -p "   Commit message: " message

        git add .
        git commit -m "$message"
        git push origin master
    fi

    # Check if we need to commit components
    for repo in ${REPOS[@]}
    do
        if git diff --quiet $repo/master master:$SUBTREE_DIR/$repo; then
            echo "   No changes in $repo"
        else
            echo "   Pushing $repo"
            git subtree push --prefix=$SUBTREE_DIR/$repo --rejoin $repo master
        fi
    done
}

function split()
{
    read -p "   Name of subtree: " subtree
    read -p "   Remote URL: " remoteurl

    # Setup subtree directory
    mkdir ../$subtree
    cd ../$subtree
    git init --bare

    # Create branch from subtree directory, call it the same thing as the subtree directory
    cd ../rdev
    git subtree split --prefix=$SUBTREE_DIR/$subtree -b $subtree
    git push ../$subtree $subtree:master

    # Push subtree to remote
    cd ../$subtree
    git remote add origin $remoteurl
    git push origin master

    # Remove original code, which will be re-added shortly
    cd ../rdev
    git rm -r $SUBTREE_DIR/$subtree

    # Setup subtree in main repo
    git commit -am "Removed $subtree for subtree split"
    git remote add $subtree $remouteurl
    git subtree add --prefix=$SUBTREE_DIR/$subtree $subtree master
}

function tag()
{
    read -p "   Tag Name: " tagname
    read -p "   Commit message: " message

    # Tag RDev
    git tag -a $tagname -m "$message"
    git push origin $tagname

    # Tag components
    for repo in ${REPOS[@]}
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
    echo "   s: Split Subtree"
    echo "   e: Exit"
    echo "--------------------------"
    read -p "   Choice: " choice

    case $choice in
        [cC]* ) commit;;
        [tT]* ) tag;;
        [sS]* ) split;;
        [eE]* ) exit 0;;
        * ) echo "   Invalid choice";;
    esac
done