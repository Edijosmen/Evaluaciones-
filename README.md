# Evaluation System

A complete PHP web application for managing evaluations with user roles, secure authentication, and MVC architecture.

## Features

- **User Management**: Registration, login, and role-based access (Admin/User)
- **Evaluation Management**: Create, edit, and manage evaluations with questions
- **Question Types**: Multiple choice, scale (1-5), and text responses
- **Assignment System**: Assign evaluations to multiple users
- **Response Collection**: Secure submission with validation
- **Admin Panel**: Full CRUD operations for users and evaluations
- **Security**: Password hashing, SQL injection protection, XSS prevention
- **Responsive UI**: Bootstrap-based interface

## Architecture

- **MVC Pattern**: Separation of concerns with Models, Views, and Controllers
- **Database**: MySQL with PDO for secure queries
- **Routing**: Custom router for clean URLs
- **Authentication**: Session-based with role checking
- **Validation**: Frontend and backend input validation

## Requirements

- PHP 8.0+
- MySQL 5.7+
- Apache/Nginx web server
- Composer (optional for dependencies)

## Installation

1. **Clone or Download** the project to your web server root (e.g., `htdocs` for XAMPP)

2. **Database Setup**:
   - Create a MySQL database named `evaluation_app`
   - Import the SQL file: `database/database.sql`

3. **Configuration**:
   - Update `.env` file with your database credentials:
     ```
     DB_HOST=localhost
     DB_NAME=evaluation_app
     DB_USER=your_username
     DB_PASS=your_password
     ```

4. **Web Server Configuration**:
   - Ensure the `public` directory is the document root
   - For XAMPP, place the project in `htdocs` and access via `http://localhost/your_project_name/public`

5. **Permissions**:
   - Ensure `storage/logs` is writable by the web server

## Usage

### Default Accounts

- **Admin**: admin@test.com / 123456
- **User**: user1@test.com / 123456

### Admin Features

- Manage users (create, edit, delete)
- Create and manage evaluations
- Add questions to evaluations
- Assign evaluations to users
- View responses

### User Features

- View assigned active evaluations
- Complete evaluations within date ranges
- Submit responses securely

## Project Structure

```
/
├── app/
│   ├── Controllers/     # Business logic
│   ├── Models/         # Data models
│   └── Logger.php      # Logging utility
├── config/
│   └── config.php      # Configuration loader
├── database/
│   └── database.sql    # Database schema and seed data
├── public/
│   └── index.php       # Entry point
├── storage/
│   └── logs/           # Application logs
├── views/
│   ├── admin/          # Admin templates
│   └── user/           # User templates
├── .env                # Environment configuration
└── README.md           # This file
```

## Security Features

- Password hashing with `password_hash()`
- Prepared statements for SQL queries
- Input sanitization and validation
- XSS protection with `htmlspecialchars()`
- Session management
- Role-based access control

## Development

- Follows SOLID principles
- Clean code with comments
- Error handling and logging
- Responsive design with Bootstrap

## License

This project is open source and available under the MIT License.
