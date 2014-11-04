if ! git diff --quiet ; then
    read -p "Commit message: " message

    git add .
    git commit -m "$message"
    git push origin master
fi

repos=(applications)

for repo in ${repos[@]}
do
    if ! git diff --quiet $repo/master master:application/rdev/$repo; then
        git subtree push --prefix=application/rdev/$repo $repo master
    fi
done