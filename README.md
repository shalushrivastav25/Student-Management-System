# Student Management System

## Project Overview

The Student Management System is a web-based application developed using HTML, CSS, PHP, MySQL, and XAMPP. It helps manage student records digitally. Users can register students, view their details, update information, and delete records through a simple dashboard.

The project is designed to make student record management easier, faster, and more organized.

## Technologies Used

- **HTML** – Creates the structure of web pages.
- **CSS** – Provides styling and layout for the application.
- **PHP** – Handles application logic and communication with the database.
- **MySQL** – Stores student information.
- **XAMPP** – Provides the local Apache web server and MySQL database environment.
- **Visual Studio Code** – Used to write and edit the project code.

## Features

- Student registration form.
- Add new student records.
- View all registered students on the dashboard.
- Edit existing student information.
- Delete student records with confirmation.
- Input validation for name, roll number, and email.
- Database connectivity using PHP and MySQL.
- User-friendly interface with navigation links.
- Student records displayed in ascending ID order.

## Project Structure

```text
student_management_system/
│
├── db.php
├── index.php
├── register.php
├── logout.php
├── style.css
└── README.md
```

### File Description

- **db.php** – Connects the PHP application to the MySQL database.
- **index.php** – Displays the dashboard and provides options to view, edit, and delete student records.
- **register.php** – Provides a form to register new students.
- **logout.php** – Clears the current PHP session and redirects to the dashboard.
- **style.css** – Defines the design, colors, forms, navigation bar, and tables.
- **README.md** – Contains project information and setup instructions.

## Database Details

Database name: `student_db`

Table name: `students`

| Column Name | Description |
|---|---|
| id | Unique student ID, usually auto-incremented |
| name | Student's full name |
| roll_no | Student's roll number |
| email | Student's email address |

## Installation and Setup

### Step 1: Install XAMPP

Download and install XAMPP on your computer.

### Step 2: Start Services

Open the XAMPP Control Panel and start:

- Apache
- MySQL

### Step 3: Create the Project Folder

Create a folder named `student_management_system` inside:

```text
C:\xampp\htdocs\
```

Place all the project files inside this folder.

### Step 4: Create the Database

1. Open your browser.
2. Visit `http://localhost/phpmyadmin/`.
3. Create a database named `student_db`.
4. Create a table named `students` with the following columns:

| Column | Data Type | Settings |
|---|---|---|
| id | INT | Primary Key, Auto Increment |
| name | VARCHAR(100) | Not Null |
| roll_no | INT | Not Null |
| email | VARCHAR(100) | Not Null |

If the database and table already exist, you do not need to create them again.

### Step 5: Configure Database Connection

Open `db.php` and verify that the connection details match your MySQL setup.

The default XAMPP configuration commonly uses:

- Host: `localhost`
- Username: `root`
- Password: blank

### Step 6: Run the Project

Open your browser and use these links:

- Registration Page: `http://localhost/student_management_system/register.php`
- Dashboard: `http://localhost/student_management_system/index.php`

## How to Use the Application

1. Open the Student Registration page.
2. Enter the student's name, roll number, and email address.
3. Click **Register Student**.
4. Open the Dashboard to view registered students.
5. Click **Edit** to update a student's information.
6. Click **Delete** to remove a student after confirmation.
7. Use the navigation bar to move between pages.

## Advantages

- Reduces manual record keeping.
- Makes student information easier to access.
- Provides a simple interface for managing records.
- Stores information in a MySQL database.
- Allows student records to be updated and deleted easily.

## Limitations

- The application currently does not have a complete admin login system.
- It runs locally using XAMPP unless deployed to a suitable web server.
- Database backups and access controls should be added for production use.

## Future Enhancements

- Add a secure admin login and logout system.
- Add student search functionality.
- Add attendance management.
- Add student marks and results management.
- Add export and print options for student records.
- Improve security and add database backup functionality.

## Conclusion

The Student Management System demonstrates how HTML, CSS, PHP, and MySQL can be combined to develop a basic database-driven web application. It provides essential operations for registering, viewing, updating, and deleting student records and helps build practical knowledge of web development and database management.

## Author

Student Project – Student Management System
