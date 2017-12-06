sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y build-essential apache2 npm git tree vim
sudo apt-get install -y php7.1 php7.1-opcache php7.1-sqlite3 php7.1-readline 
                      php7.1-mysql php7.1-mcrypt php7.1-mbstring php7.1-json php7.1-intl php7.1-gd 
                      php7.1-curl php7.1-bz2 php7.1-bcmath php7.1-cli php7.1-zip php7.1-xml
                      
sudo apt-get install -y composer pkg-config php-dompdf php-pear php-imagick autoconf automake libtool m4
sudo apt-get install -y redis-server
sudo apt-get install -y mysql-server
sudo apt-get install -y screen autoconf automake libtool m4 pkg-config
sudo apt-get install -y php-pear unzip python-pip supervisor
sudo apt-get install -y composer pkg-config php-dompdf php-pear php-imagick autoconf automake libtool m4
sudo apt-get install php7.1-dev
sudo pecl channel-update pecl.php.net
sudo pecl install trader
