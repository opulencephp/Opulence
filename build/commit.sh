REPOS=(Applications Authentication Bootstrappers Cache Console Cryptography Databases Debug Environments Events Files Framework Http Ioc Memcached Orm Pipelines QueryBuilders Redis Routing Sessions Users Views)
SUBTREE_DIR="src/Opulence"
APPLICATION_CLASS_FILE="$SUBTREE_DIR/Applications/Application.php"
REMOTE_URL="https://github.com/opulencephp"

function split()
{
    git co master
    read -p "   Name of subtree (case sensitive): " subtree
    lowersubtree=$(echo "$subtree" | awk '{print tolower($0)}')
    remoteurl="https://github.com/opulencephp/$lowersubtree.git"

    # Setup subtree directory
    mkdir ../$subtree
    cd ../$subtree
    git init --bare

    # Create branch from subtree directory, call it the same thing as the subtree directory
    cd ../opulence
    git subtree split --prefix=$SUBTREE_DIR/$subtree -b $subtree
    git push ../$subtree $subtree:master

    # Push subtree to remote
    cd ../$subtree
    git remote add origin $remoteurl
    git push origin master

    # Remove original code, which will be re-added shortly
    cd ../opulence
    git rm -r $SUBTREE_DIR/$subtree

    # Setup subtree in main repo
    git commit -am "Removed $subtree for subtree split"
    git remote add $subtree $remoteurl
    git subtree add --prefix=$SUBTREE_DIR/$subtree $subtree master
}

function tag()
{
    hasacknowledgedprompt=false
    read -p "   Tag Name: " tagname
    read -p "   Is Stable?: " isstable

    if [ "$isstable" == "y" ]; then
        isstable=true
    else
        isstable=false
    fi

    while ( [ $isstable == false ] && [ $hasacknowledgedprompt == false ] ); do
        read -p "   Have you manually updated version constant on master branch?: " hasupdated

        if [ "$hasupdated" == "y" ]; then
            hasacknowledgedprompt=true
        fi
    done

    read -p "   Commit message: " message

    git co master

    if $isstable; then
        # Update version
        # Remove "v" from tag name
        shorttagname=${tagname:1}
        sed -i "s/private static \$version = \"[0-9\.]*\";/private static \$version = \"$shorttagname\";/" $APPLICATION_CLASS_FILE

        # Commit changes to application file
        git commit -m "Incrementing version" $APPLICATION_CLASS_FILE
        git push origin master
    fi

    # Check if we need to commit components
    for repo in ${REPOS[@]}
    do
        if git diff --quiet $repo/master master:$SUBTREE_DIR/$repo; then
            echo "   No changes in $repo"
        else
            echo "   Pushing $repo"
            git push $repo $(git subtree split --prefix=$SUBTREE_DIR/$repo master):master --force
        fi
    done

    # Tag Opulence
    git tag -a $tagname -m "$message"
    git push origin $tagname

    # Tag components
    for repo in ${REPOS[@]}
    do
        cd ../$repo
        echo "   Tagging $repo"
        git tag -a $tagname -m  "$message"
        git push origin $tagname
    done

    cd ../opulence
}

while true; do
    # Display options
    echo "   Select an action"
    echo "   t: Tag"
    echo "   s: Split Subtree"
    echo "   e: Exit"
    echo "--------------------------"
    read -p "   Choice: " choice

    case $choice in
        [tT]* ) tag;;
        [sS]* ) split;;
        [eE]* ) exit 0;;
        * ) echo "   Invalid choice";;
    esac
done