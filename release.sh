#!/bin/bash

versionFile="current"

line=$(head -n 1 $versionFile)

readarray -td. a <<<"$line"; 
major=${a[0]}
minor=${a[1]}
rev=${a[2]}
newRev=$((rev+=1))

version=$major.$minor.$newRev
echo $version
echo $version > $versionFile

gh release create $version --notes "new version"