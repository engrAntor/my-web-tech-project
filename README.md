# PHP Student Management System

This is a project for my web technologies course, built with PHP, JavaScript, HTML and a MySQL database.

## Features

*   User registration and login
*   Course enrollment
*   Admin dashboard to manage students and courses
*   View transaction history

## How to Run This Project

This project is designed to run in a local server environment like XAMPP.

1.  Download: Download the project files and place them in your `htdocs` folder inside your XAMPP installation directory.
2.  Dependencies: You must have [Composer](https://getcomposer.org/) installed. Open a terminal in the project folder and run:
    ```bash
    composer install
    ```
    This will download PHPMailer into a `vendor` folder.
3.  Database: The `Database` folder contains the `.sql` file to set up the database structure. Import this file into your phpMyAdmin.
4.  Configuration: Rename the `config.example.php` file to `config.php`. Open it and fill in your database and email credentials.
5.  Run: Start your Apache and MySQL servers in XAMPP. Navigate to `http://localhost/MyProjectPartWebTech` in your browser.
