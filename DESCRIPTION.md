Brief description about the project and how to start it

1. Download and install Composer on your computer from the official (https://getcomposer.org/) website, considering what OS you have (Windows, Linux or macOS)
2. Install a relational database management system (RDBMS) like a Mysql and create a database locally with unicode like a utf8mb4_general_ci or utf8_general_ci and named Laravel_todo_api or how you want
3. We also need an application with a web interface for administering the RDBMS, it can be phpmyadmin (https://www.phpmyadmin.net/) or workbench (https://www.mysql.com/products/workbench/)
4. Also, for testing the API, we need the Postman application, which can be downloaded from here (https://www.postman.com/downloads/) and installed, taking into account which operating system you have
5. Open your IDE and create a folder and using Git, clone the repository there: git clone https://github.com/Larionov-Yurii/Laravel_todo_api.git
6. After cloning the repository, using terminal in IDE, we need to use the command (cd) change to the directory: cd Laravel_todo_api
7. After that we need to run (composer install) or (php composer.phar install)
8. Using the command (cp) copy the (.env.example) file and create a new (.env) file in the same directory: cp .env.example .env
9. After that we need to set (.env) file and namely: 1) DB_DATABASE - your database name 2) DB_USERNAME - username in Mysql 3) DB_PASSWORD - password in Mysql 4) APP_KEY - to get this key, we need to run command in terminal like: php artisan key:generate
10. We also need to run migrations: php artisan migrate
11. And finally we can start the project: php artisan serve
12. The launch of the project begins with the fact that we launch the Postman application, and thanks to the file (api.php), we can enter certain routes for sending a request and receiving a certain response
