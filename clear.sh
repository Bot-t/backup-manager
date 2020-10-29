#!/bin/bash

days=7

num=$(expr $days + 1)
DIR=$(
  cd "$(dirname "$0")"
  pwd
)
for file in $(ls $DIR); do
  path=$DIR"/"$file
  if [ -d $path ]; then
    cd $path && rm -rf $(ls -t | tail -n +$num)
  fi
done
