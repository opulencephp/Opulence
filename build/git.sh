REPOS=(Applications Authentication Authorization Cache Console Cryptography Databases Debug Environments Events Files Framework Http Ioc Memcached Orm Pipelines QueryBuilders Redis Routing Sessions Validation Views)
SUBTREE_DIR="src/Opulence"

function checkOutPullRequest()
{
    read -p "   Repository Name: " repository
    read -p "   Branch to Merge to: " opulencebranch
    read -p "   Username: " username
    read -p "   Branch to Pull From: " userbranch

    # Check out pull request
    cd ../$repository
    git co -b $username-$userbranch $opulencebranch
    git pull https://github.com/$username/$repository.git $userbranch

    # Switch back to home directory
    cd ../opulence
}

function mergePullRequest()
{
    read -p "   Repository Name: " repository
    read -p "   Branch to Merge to: " opulencebranch
    read -p "   Username: " username
    read -p "   Branch to Pull From: " userbranch

    # Merge pull request
    cd ../$repository
    git co $opulencebranch
    git merge --squash $username-$userbranch
    git push origin $opulencebranch
    git branch -d $username-$userbranch
    echo "Remember to merge to other appropriate branches"

    # Switch back to home directory
    cd ../opulence
}

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
    read -p "   Commit message: " message

    git co master

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

    // Switch back to home directory
    cd ../opulence
}

while true; do
    # Display options
    echo "   Select an action"
    echo "   t: Tag"
    echo "   s: Split Subtree"
    echo "   c: Check Out Pull Request"
    echo "   m: Merge Pull Request"
    echo "   e: Exit"
    echo "--------------------------"
    read -p "   Choice: " choice

    case $choice in
        [tT]* ) tag;;
        [sS]* ) split;;
        [cC]* ) checkOutPullRequest;;
        [mM]* ) mergePullRequest;;
        [eE]* ) exit 0;;
        * ) echo "   Invalid choice";;
    esac
done