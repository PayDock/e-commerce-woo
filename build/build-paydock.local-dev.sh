php plugin-generate.php config-paydock.php "local"
php readme-generate-paydock.php "local"
php composer-generate.php config-paydock.php "local"
cd ..
composer update

echo "Local build for Paydock concluded"
