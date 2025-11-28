#!/bin/bash

REPO_PATH=$PWD
rm -rf /tmp/guardiankeyauth
mkdir /tmp/guardiankeyauth
cp -r * /tmp/guardiankeyauth
cd /tmp/guardiankeyauth
rm -rf .git test install.sh guardiankeyauth.zip
cd /tmp
zip -r guardiankeyauth.zip guardiankeyauth
cd $REPO_PATH
cp /tmp/guardiankeyauth.zip .

