-- 1. Create the databases
CREATE DATABASE IF NOT EXISTS wordpress_sitehaat;
CREATE DATABASE IF NOT EXISTS wordpress_akhlaqfood;
CREATE DATABASE IF NOT EXISTS wordpress_grocery;

-- 2. Ensure the user exists (This prevents the 1410 error)
-- Replace 'your_password_here' with the actual password you want
CREATE USER IF NOT EXISTS 'comarto_user_656'@'%' IDENTIFIED BY 'your_password_here';

-- 3. Grant privileges
GRANT ALL PRIVILEGES ON wordpress_sitehaat.* TO 'comarto_user_656'@'%';
GRANT ALL PRIVILEGES ON wordpress_akhlaqfood.* TO 'comarto_user_656'@'%';
GRANT ALL PRIVILEGES ON wordpress_grocery.* TO 'comarto_user_656'@'%';

-- 4. Apply changes
FLUSH PRIVILEGES;
