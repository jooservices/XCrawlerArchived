#!/bin/bash

echo Change directory
cd /home/pi/XCrawler # Change to your installed path

echo Take down
php artisan down
sudo /etc/init.d/supervisor stop # Change command that used on your system

echo Updating
git pull
composer install
php artisan optimize:clear
php artisan optimize
php artisan migrate --force
php artisan queue:restart

echo Bring up
php artisan up
sudo /etc/init.d/supervisor start
