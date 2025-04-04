#!/bin/sh

php ../plugin-generate.php config-powerboard.php "local"
php readme-generate-powerboard.php "local"
php phpcs-generate-powerboard.php "local"

cp "./logo.png" "../../assets/images/logo.png"

echo "Local build for PowerBoard concluded"
