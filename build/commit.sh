if ! git diff --quiet ; then
    read -p "   Commit message: " message

    git add .
    git commit -m "$message"
    git push origin master
fi

repos=(applications authentication configs cryptography databases exceptions files http ioc orm routing sessions users views)

for repo in ${repos[@]}
do
    if git diff --quiet $repo/master master:application/rdev/$repo; then
        echo "   No changes in $repo"
    else
        git subtree push --prefix=application/rdev/$repo --squash $repo master
    fi
done