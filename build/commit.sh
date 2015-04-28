REPOS=(applications authentication console cryptography databases files forms framework http ioc memcached orm pipelines querybuilders redis routing sessions users views)
SUBTREE_DIR="app/rdev"
APPLICATION_CLASS_FILE="$SUBTREE_DIR/applications/Application.php"

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
    git remote add $subtree $remoteurl
    git subtree add --prefix=$SUBTREE_DIR/$subtree $subtree master
}

function tag()
{
    read -p "   Tag Name: " tagname
    read -p "   Commit message: " message

    # Update version
    # Remove "v" from tag name
    shorttagname=${tagname:1}
    sed -i "s/private static \$version = \"[0-9\.]*\";/private static \$version = \"$shorttagname\";/" $APPLICATION_CLASS_FILE

    # Commit changes to application file
    git commit -m "$message" $APPLICATION_CLASS_FILE
    git push origin master
    git subtree push --prefix=$SUBTREE_DIR/applications --rejoin applications master

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