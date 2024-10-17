php plugin-generate.php config-powerboard.php "local"
php readme-generate-powerboard.php "local"
php composer-generate.php config-powerboard.php "local"
cd ..
composer update

echo "Local build for PowerBoard concluded"
