Instructions for running (windows)
1. download xampp and install
2. Launch the xampp control panel
3. Start the apache and mySQL servers
4. launch the mySQL admin, phpMyAdmin
5. Run SQL commands to create a database and a user and give user permissions on the db
	CREATE DATABASE todo;
	CREATE USER 'todo'@'localhost' IDENTIFIED BY 'todopass123';
	GRANT ALL PRIVILEGES ON todo.* TO 'todo'@'localhost';
6. move todo.php to the htdocs folder in the xampp install directory */xampp/htdocs/todo.php
7. go to 127.0.0.1/todo.php to test the webapp