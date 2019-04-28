REPOS=(Authentication Authorization Cache Collections Console Cryptography Databases Debug Environments Events Framework Http IO Ioc Memcached Orm Pipelines QueryBuilders Redis Routing Sessions Validation Views)
BRANCHES_TO_SPLIT=(master 2.0)

function checkOutPullRequest()
{
    read -p "   Repository Name: " repository
    read -p "   Branch to Merge to: " opulencebranch
    read -p "   Username: " username
    read -p "   Branch to Pull From: " userbranch

    # Check out pull request
    cd ../$repository
    git checkout -b $username-$userbranch $opulencebranch
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
    git checkout $opulencebranch
    git merge --squash $username-$userbranch
    git commit
    git push origin $opulencebranch
    git branch -d $username-$userbranch
    echo "Remember to merge to other appropriate branches"

    # Switch back to home directory
    cd ../opulence
}

function tag()
{
    hasacknowledgedprompt=false
    read -p "   Tag Name: " tagname
    read -p "   Commit message: " message

    git checkout master

    # Tag Opulence
    git tag -a $tagname -m "$message"
    git push origin $tagname

    # Initialize (if necessary) and update the subtree
    git subsplit init git@github.com:opulencephp/Opulence.git
    git subsplit update

    for repo in ${REPOS[@]}
    do
        lowerrepo=$(echo "$repo" | awk '{print tolower($0)}')
        git subsplit publish src/Opulence/$repo:git@github.com:opulencephp/$lowerrepo.git --heads="${BRANCHES_TO_SPLIT[@]}" --tags="$tagname"
    done
}

while true; do
    # Display options
    echo "   Select an action"
    echo "   t: Tag"
    echo "   c: Check Out Pull Request"
    echo "   m: Merge Pull Request"
    echo "   e: Exit"
    echo "--------------------------"
    read -p "   Choice: " choice

    case $choice in
        [tT]* ) tag;;
        [cC]* ) checkOutPullRequest;;
        [mM]* ) mergePullRequest;;
        [eE]* ) exit 0;;
        * ) echo "   Invalid choice";;
    esac
done