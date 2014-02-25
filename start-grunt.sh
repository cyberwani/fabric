#!/bin/sh

#cd to the projects root directory
cd "$( dirname "$0" )"

#Install Grunt and dependecies in project root directory based on "pacakge.json"
sudo npm install

#Start Grunt
grunt dev
