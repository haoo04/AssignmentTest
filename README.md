# Forum System

This is a simple forum system built with PHP. Users can register, log in, post threads, comment on posts, and search for threads.

## Main Features

*   **User Authentication**: Includes user registration, login, and logout functionality.
*   **Post Management**: Users can create, edit, and delete their own posts.
*   **Comment System**: Users can comment on posts.
*   **Post Search**: Supports keyword-based post searching.
*   **User Profiles**: Users can upload and display profile pictures.

## Tech Stack

*   **Backend**: PHP
*   **Frontend**: HTML, CSS, JavaScript
*   **Database**: MySQL / MariaDB
*   **Web Server**: Apache 

## Project Structure

```
AssignmentTest/
├── assets/         # Stores static resources such as CSS and JavaScript
├── controllers/    # Controllers that handle business logic
├── models/         # Models that handle data interaction and database operations
├── uploads/        # Stores user-uploaded files (e.g., profile pictures)
├── views/          # Views responsible for rendering pages
├── auth.php        # User authentication
├── config.php      # Database connection and basic configuration
├── index.php       # Main entry point of the website
├── login.php       # User login page
├── logout.php      # User logout
└── registration.php # User registration page
```

## Installation & Deployment

Follow the steps below to set up the project in your local environment.

### 1. Environment Requirements

*   [XAMPP](https://www.apachefriends.org/) or another AMP (Apache, MySQL, PHP) stack.
*   PHP version 7.4 or above.
*   MySQL or MariaDB.

### 2. Clone the Project

Clone or download the project into the root directory of your web server (e.g., htdocs folder in XAMPP).

### 3. Database Setup

1.  Open `phpMyAdmin` or any other database management tool.
2.  Create a new database
3.  Execute the `recipe_web.sql` SQL file to create the necessary tables:

### 4. Configuration File

1.  Open `config.php` 
2.  Modify the database connection settings according to your local environment:

    ```php
    // Database Configuration
    $host = "localhost";
    $user = "root"; // Your database username
    $password = ""; // Your database password
    $dbname = "recipe_web"; // The name of the database you created
    ```

### 5. Run the Project

1.  Start Apache and MySQL in XAMPP or your server environment.
2.  Visit `http://localhost/CookSmart/` in your browser (assuming your project folder is named `AssignmentTest`).
3.  You should see the homepage. Now you can register a new user and start using the forum!