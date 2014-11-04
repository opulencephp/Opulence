PULLING FROM SUBTREE MASTER
============================
git subtree pull --prefix=application/rdev/{name-of-repo} --squash {name-of-branch-(same-as-repo)} master

PUSHING TO SUBTREE MASTER
============================
git subtree push --prefix=application/rdev/{name-of-repo} --squash {name-of-branch-(same-as-repo)} master