read -p "Commit message: " message

git add .
git commit -m "$message"
git push origin master

repos=(applications)

for repo in ${repos[@]}
do
    if ! git diff $repo/master master:application/rdev/$repo; then
        git subtree push --prefix=application/rdev/$repo $repo master
    fi
done