if [ $# == 0 ]; then
    echo "Please enter commit message"
    exit 2
fi

git add .
git commit -m "$1"
git push origin master