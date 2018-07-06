#!/bin/sh
if [ -z "$1" ] ; then
    php bin/console doctrine:migrations:migrate
    exit
fi

direction='down'

case $2 in
    "u") direction='up';;
esac

if [ $1 = "diff" ] ; then
    php bin/console doctrine:migrations:diff
    exit
fi

php bin/console doctrine:migrations:execute $1 --$direction