if git diff applications/master master:application/rdev/applications; then
    git subtree push --prefix=application/rdev/applications --squash applications master
fi