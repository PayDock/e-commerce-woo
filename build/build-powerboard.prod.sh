php build/plugin-generate.php config-powerboard.php "prod"
php build/readme-generate-powerboard.php "prod"
php build/composer-generate.php config-powerboard.php "prod"
composer update

echo "Build for PowerBoard concluded"
