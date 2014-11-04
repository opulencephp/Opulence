if git diff-tree views/master --quiet HEAD --; then
    git subtree push --prefix=application/rdev/views --squash views master
fi