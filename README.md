# 🎓 Student Management System

A comprehensive Laravel-based school management system with multi-role authentication, student/teacher management, attendance tracking, marks management, fee management, and timetable creation.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

---

## ✨ Features

### 👥 Multi-Role System
- **Admin**: Full system control
- **Teacher**: Student & academic management
- **Student**: View personal records & report cards

### 🔐 Advanced Authentication
- ✅ Password-based login (Admin)
- ✅ Passwordless magic link login (Teachers & Students)
- ✅ Password reset via email
- ✅ Remember me functionality (60 days)
- ✅ Email verification support
- ✅ Self-service portal link request

### 📚 Academic Management
- ✅ Student CRUD operations
- ✅ Teacher CRUD operations
- ✅ Class & section management
- ✅ Subject management
- ✅ Attendance tracking (bulk entry)
- ✅ Marks/grades management
- ✅ Fee management with payment tracking
- ✅ Timetable creation
- ✅ Report card generation

### 🎨 Modern UI/UX
- ✅ Responsive design (mobile-friendly)
- ✅ Bootstrap 5 components
- ✅ DataTables integration
- ✅ AJAX-powered interfaces
- ✅ Avatar system with fallback to UI Avatars
- ✅ Password strength meter
- ✅ Dark theme design

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & npm

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd student-management-system
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
```
Edit `.env` and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_management_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. **Generate application key**
```bash
php artisan key:generate
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Create storage symlink**
```bash
php artisan storage:link
```

7. **Seed database (optional)**
```bash
php artisan db:seed
```

8. **Start development server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## 📖 Documentation

Detailed documentation available in:

### 📄 [DATABASE_DOCUMENTATION.md](DATABASE_DOCUMENTATION.md)
Complete database structure, column explanations, and how authentication features work.

### 🧪 [TEST_FEATURES.md](TEST_FEATURES.md)
Comprehensive testing guide for all features including password reset, remember me, and email verification.

### 📋 [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)
High-level project overview, architecture, and usage guide.

---

## 🗄️ Database Structure

### Core Tables
- `users` - User accounts (Admin, Teachers, Students)
- `students` - Student information
- `teachers` - Teacher information
- `school_classes` - Classes (Class 1, Class 2, etc.)
- `sections` - Class sections (A, B, C)
- `subjects` - Academic subjects
- `attendances` - Daily attendance records
- `marks` - Exam marks/grades
- `fees` - Fee management
- `timetables` - Class schedules
- `password_reset_tokens` - Password reset tokens
- `sessions` - User sessions

---

## 🔑 Default Access

### Admin Login
```
Email: admin@school.com
Password: (set during seeding or create manually)
```

### Teacher/Student Login
- Request portal access link via email
- Or admin can generate and send access link
- Link valid for 48 hours

---

## 🛣️ Key Routes

### Authentication
- `/login` - Login page
- `/forgot-password` - Password reset request
- `/reset-password/{token}` - Reset password form
- `/request-portal-link` - Self-service portal link request
- `/portal-access/{user}` - Magic link authentication

### Admin Panel
- `/admin/dashboard` - Admin dashboard
- `/admin/teachers` - Teacher management
- `/admin/classes` - Class management
- `/admin/subjects` - Subject management

### Teacher Panel
- `/teacher/dashboard` - Teacher dashboard
- `/students` - Student management
- `/attendance` - Attendance tracking
- `/marks` - Marks entry
- `/fees` - Fee management
- `/timetables` - Timetable creation

### Student Portal
- `/student/dashboard` - Student dashboard
- `/students/{id}/report-card` - Report card

---

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ CSRF protection
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade escaping)
- ✅ Role-based authorization
- ✅ Signed URLs for magic links
- ✅ Rate limiting on sensitive endpoints
- ✅ HttpOnly cookies
- ✅ Password reset token expiry (60 minutes)
- ✅ Remember token regeneration on password change

---

## 📊 Project Statistics

### Languages Used
- **Blade (PHP Templates)**: 62.66%
- **PHP (Backend)**: 22.61%
- **JavaScript**: 8.31%
- **CSS**: 6.42%

### Tech Stack
- **Framework**: Laravel 12.x
- **Database**: MySQL
- **Frontend**: Blade Templates + Bootstrap 5
- **JavaScript**: jQuery + DataTables
- **Icons**: Bootstrap Icons

---

## 🧪 Testing

### Quick Verification
```bash
# Check database structure
php artisan tinker
Schema::hasColumn('users', 'avatar');
Schema::hasTable('password_reset_tokens');
exit

# Test routes
php artisan route:list

# View migrations status
php artisan migrate:status
```

### Feature Testing
See [TEST_FEATURES.md](TEST_FEATURES.md) for detailed testing instructions.

---

## 📧 Email Configuration

For password reset and notifications to work, configure email in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@school.com
MAIL_FROM_NAME="Student Management System"
```

---

## 🎨 UI Screenshots

The system features a modern, dark-themed interface with:
- Gradient backgrounds
- Card-based layouts
- Responsive tables with DataTables
- Avatar thumbnails
- Smooth animations
- Password strength indicators

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Developer

**Project Status**: ✅ Production Ready

**Created**: July 2026

---

## 📞 Support

For issues or questions:
1. Check the documentation files
2. Review Laravel documentation: https://laravel.com/docs
3. Open an issue on GitHub

---

## 🎯 Future Enhancements

- [ ] PDF report card generation
- [ ] Bulk student import (CSV/Excel)
- [ ] Email notification queue
- [ ] Parent portal access
- [ ] SMS notifications
- [ ] Advanced reporting & analytics
- [ ] Multi-language support
- [ ] Dark mode toggle
- [ ] Activity logs/audit trail

---

**Happy Managing! 🎓**
