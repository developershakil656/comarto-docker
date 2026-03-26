CREATE DATABASE IF NOT EXISTS wordpress_sitehaat;
CREATE DATABASE IF NOT EXISTS wordpress_akhlaqfood;
CREATE DATABASE IF NOT EXISTS wordpress_grocery;

GRANT ALL PRIVILEGES ON wordpress_sitehaat.* TO 'b2buser'@'%';
GRANT ALL PRIVILEGES ON wordpress_akhlaqfood.* TO 'b2buser'@'%';
GRANT ALL PRIVILEGES ON wordpress_grocery.* TO 'b2buser'@'%';

FLUSH PRIVILEGES;
