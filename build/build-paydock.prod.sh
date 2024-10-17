php build/plugin-generate.php config-paydock.php "prod"
php build/readme-generate-paydock.php "prod"
php build/composer-generate.php config-paydock.php "prod"
composer update

echo "Build for Paydock concluded"
