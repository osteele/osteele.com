## Setup

    brew install hugo
    git subtree add --prefix=public git@github.com:osteele/osteele.com.git gh-pages --squash

## Iterate

    git commit -m "Updating site"
    git subtree push --prefix=public git@github.com:osteele/osteele.com.git gh-pages
