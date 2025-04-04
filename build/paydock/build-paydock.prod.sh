#!/bin/sh

php build/plugin-generate.php paydock/config-paydock.php "prod"
php build/paydock/readme-generate-paydock.php "prod"

cp "build/paydock/logo.png" "assets/images/logo.png"

echo "Build for Paydock concluded"
