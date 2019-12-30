# Execute this with elevated permissions to install dependencies.

linux_distribution=$( python -c 'import platform;print(platform.linux_distribution()[0])' )
echo installing for $linux_distribution

# Debian 9 - nginx
# apt:php-fpm
# apt:nginx
# apt:postgres-all

if [ "$linux_distribution" = "Ubuntu" ]
then
    # Ubuntu 18 - PHP microserver
    apt install php7.2-cli
    apt install php7.2-sqlite3
fi