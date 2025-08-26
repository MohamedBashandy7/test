# Project Management System API

<p align="center">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## 📋 Overview

A robust RESTful API for managing projects, tasks, and user roles with a focus on project approval workflows and task management.

## ✨ Features

- **User Authentication & Authorization**
  - JWT-based authentication
  - Role-based access control (Admin, Project Manager, Developer)
  - Email verification

- **Project Management**
  - Create, read, update, and delete projects
  - Project approval workflow
  - Project status tracking
  - File attachments support

- **Task Management**
  - Task creation and assignment
  - Status updates
  - Due date tracking
  - File attachments

- **Analytics**
  - Project statistics
  - Task completion rates
  - User activity tracking

## 🚀 Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js & NPM

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/project-management-api.git
   cd project-management-api
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database**
   Update your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=project_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Configure Mail Server**
   Update your `.env` file with your mail server credentials to enable email verification and notifications:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@example.com
   MAIL_PASSWORD=your-email-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Run Migrations**
   ```bash
   php artisan migrate --seed
   ```

7. **Start Development Server**
   ```bash
   php artisan serve
   ```

### Testing Authentication

1. **Register a new user**
   ```bash
   curl -X POST http://localhost:8000/api/register \
     -H "Content-Type: application/json" \
     -d '{
       "name": "Test User",
       "email": "test@example.com",
       "password": "password",
       "password_confirmation": "password"
     }'
   ```
   
   You'll receive a verification email with a verification code.

2. **Verify Email**
   ```bash
   curl -X POST http://localhost:8000/api/verify \
     -H "Content-Type: application/json" \
     -d '{
       "email": "test@example.com",
       "verification_code": "YOUR_VERIFICATION_CODE"
     }'
   ```

3. **Login**
   ```bash
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{
       "email": "test@example.com",
       "password": "password"
     }'
   ```
   
   This will return an access token to use for authenticated requests.

## 📚 API Documentation

### Authentication

All protected routes require an authentication token. Include the token in the request header:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

### Available Endpoints

#### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user

#### Projects
- `GET /api/projects` - List all projects
- `POST /api/projects` - Create a new project
- `GET /api/projects/{project}` - Get project details
- `PUT /api/projects/{project}` - Update project
- `DELETE /api/projects/{project}` - Delete project
- `POST /api/projects/{project}/status` - Update project status (approve/reject)
- `GET /api/projects/pending` - Get pending projects

#### Tasks
- `GET /api/tasks` - List all tasks
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{task}` - Get task details
- `PUT /api/tasks/{task}` - Update task
- `DELETE /api/tasks/{task}` - Delete task
- `PUT /api/tasks/{task}/status` - Update task status

#### Analytics
- `GET /api/stats` - Get system statistics

## 🧪 Testing

Run the test suite with:

```bash
php artisan test
```

## 🔒 Security

If you discover any security vulnerabilities, please email security@example.com instead of using the issue tracker.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Contributions are welcome! Please read our [contributing guidelines](CONTRIBUTING.md) before submitting pull requests.

## 📧 Support

For support, email support@example.com or open an issue in the GitHub repository.

---

Built with ❤️ using [Laravel](https://laravel.com)

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

For support, email support@example.com or open an issue in the GitHub repository.

---

Built with ❤️ using [Laravel](https://laravel.com)
