#!/bin/bash

read -p "Enter username: " user
read -p "Enter DB Name: " dbname
read -p "Enter PWD: " passwd
read -p "Absolute path in to current directory: " path


psql "dbname='$dbname' user='$user' password='$passwd' host='mcsdb.utm.utoronto.ca'" -f ./admin/schema.sql



establishConnection="pg_connect(\"host=mcsdb.utm.utoronto.ca dbname=$dbname user=$user password=$passwd\");"
path1="AuthUserFile $path/htpasswd"
sed -i "s/pg_connect(\".*/${establishConnection}/g" ./api/api.php
sed -i "s+AuthUserFile .*+${path1}+g" ./.htaccess
