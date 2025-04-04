#!/bin/sh

php ../plugin-generate.php config-paydock.php "local"
php readme-generate-paydock.php "local"
php phpcs-generate-paydock.php "local"

cp "./logo.png" "../../assets/images/logo.png"

echo "Local build for Paydock concluded"
