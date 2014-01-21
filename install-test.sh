#!/bin/bash
sh -c "mysql -e 'create database IF NOT EXISTS siteforever_test;'"
curl -sS https://getcomposer.org/installer | php
php composer.phar install --prefer-source --no-dev -o --no-interaction
