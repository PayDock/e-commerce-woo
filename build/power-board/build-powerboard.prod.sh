#!/bin/sh

php build/plugin-generate.php power-board/config-powerboard.php "prod"
php build/power-board/readme-generate-powerboard.php "prod"

cp "build/power-board/logo.png" "assets/images/logo.png"

echo "Build for PowerBoard concluded"
