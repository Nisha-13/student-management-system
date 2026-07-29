# 📚 Student Management System - Database Documentation

## 🗄️ Database Tables Overview

Yeh document Laravel Student Management System ke **complete database structure** ko explain karta hai.

---

## 👤 **1. USERS TABLE**

User accounts store karta hai (Admin, Teachers, Students ke liye)

### Columns:

| Column | Type | Purpose | Example |
|--------|------|---------|---------|
| `id` | bigint (PK) | Unique user identifier | 1, 2, 3 |
| `name` | varchar(255) | User ka full name | "Ali Ahmed" |
| `email` | varchar(255) UNIQUE | Login ke liye email address | "ali@school.com" |
| `role` | enum | User type: admin/teacher/student | "student" |
| `email_verified_at` | timestamp NULL | Email verification ka time | "2026-07-29 10:30:00" |
| `password` | varchar(255) | Hashed password (bcrypt) | "$2y$10$..." |
| `remember_token` | varchar(100) NULL | "Remember Me" feature ke liye | "xYz123..." |
| `avatar` | varchar(255) NULL | Profile photo path | "avatars/user123.jpg" |
| `created_at` | timestamp | Account creation date/time | "2026-01-15 09:00:00" |
| `updated_at` | timestamp | Last update time | "2026-07-29 11:45:00" |

### Detailed Explanation:

#### 🔐 **email_verified_at**
```php
Purpose: Email verification track karta hai
- NULL = Email abhi verify nahi hua
- Timestamp = Email verify ho chuka hai (verification link click karne ke baad)

Use Case:
- User registration ke baad verification email bhejte hain
- Link click karne par yeh column timestamp set hota hai
- Verified users ko hi sensitive features allow karte hain

Example:
$user->email_verified_at = now(); // Email verify karne par
if ($user->hasVerifiedEmail()) {
    // Allow full access
}
```

#### 🍪 **remember_token**
```php
Purpose: "Remember Me" checkbox functionality ke liye
- Login form mein "Remember Me" check karne par use hota hai
- 60 days tak auto-login rehta hai (without password)
- Cookie mein store hota hai

How it Works:
1. User login karte waqt "Remember Me" check karta hai
2. Laravel random token generate karta hai
3. Token database + browser cookie dono mein save hota hai
4. Next visit par token match karke auto-login ho jata hai

Example Code:
// Login with remember me
if (Auth::attempt($credentials, $remember = true)) {
    // Token automatically generated and stored
}

// Laravel automatically checks token on next request
// No password needed for 60 days!
```

#### 📸 **avatar**
```php
Purpose: User profile picture store karta hai
- File path store hota hai (actual image storage/app/public mein)
- NULL hone par fallback UI Avatar (initials) show hota hai

Example:
"avatars/user_123_1627584926.jpg"

Access:
$user->avatar_url // Returns full URL for display
```

---

## 🔑 **2. PASSWORD_RESET_TOKENS TABLE**

"Forgot Password" feature ke liye temporary tokens store karta hai

### Columns:

| Column | Type | Purpose | Example |
|--------|------|---------|---------|
| `email` | varchar(255) PK | User ka email address | "ali@school.com" |
| `token` | varchar(255) | Hashed reset token | "$2y$10$..." |
| `created_at` | timestamp | Token creation time | "2026-07-29 10:00:00" |

### How Password Reset Works:

```php
Flow:
1. User "Forgot Password" pe click karta hai
2. Email enter karta hai
3. System random token generate karta hai
4. Token hash karke database mein save hota hai
5. Reset link email mein bhejta hai:
   https://yoursite.com/reset-password?token=abc123&email=ali@school.com
   
6. User link click karta hai
7. Token verify hota hai (database se match)
8. Agar valid hai (aur 60 minutes ke andar hai):
   - New password set karne ka form show hota hai
   - Password update hone ke baad token delete ho jata hai

Security Features:
- Token hashed form mein store (plain text nahi)
- 60 minutes ke baad expire (config/auth.php mein set)
- Use hone ke baad delete
- One-time use only

Code Example:
// Request password reset
Password::sendResetLink(['email' => $request->email]);

// Reset password
Password::reset($credentials, function ($user, $password) {
    $user->password = Hash::make($password);
    $user->save();
});
```

### Token Cleanup:
```php
// Old tokens (>60 min) automatically clean nahi hote
// Optional: Cron job se cleanup
DB::table('password_reset_tokens')
    ->where('created_at', '<', now()->subHours(1))
    ->delete();
```

---

## 🧑‍🎓 **3. STUDENTS TABLE**

Student-specific information (user table se extend karta hai)

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique student ID |
| `user_id` | bigint FK | Link to users table |
| `school_class_id` | bigint FK | Student kis class mein hai |
| `section_id` | bigint FK | Student kis section mein hai |
| `roll_number` | varchar UNIQUE | Class roll number |
| `dob` | date NULL | Date of birth |
| `gender` | enum | male/female/other |
| `phone` | varchar NULL | Contact number |
| `address` | text NULL | Home address |
| `avatar` | varchar NULL | Student photo path |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

### Relationships:
```php
Student belongsTo User (authentication ke liye)
Student belongsTo SchoolClass (class assignment)
Student belongsTo Section (section assignment)
Student hasMany Attendances (daily attendance records)
Student hasMany Marks (exam marks)
Student hasMany Fees (fee records)
```

---

## 👨‍🏫 **4. TEACHERS TABLE**

Teacher-specific information

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique teacher ID |
| `user_id` | bigint FK | Link to users table |
| `employee_id` | varchar | Official employee ID |
| `phone` | varchar NULL | Contact number |
| `qualification` | varchar NULL | Educational qualification |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 📚 **5. SCHOOL_CLASSES TABLE**

Classes (Class 1, Class 2, etc.)

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique class ID |
| `name` | varchar | Class name (e.g., "Class 10") |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 📑 **6. SECTIONS TABLE**

Class sections (A, B, C, etc.)

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique section ID |
| `school_class_id` | bigint FK | Belongs to which class |
| `name` | varchar | Section name (e.g., "A") |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 📖 **7. SUBJECTS TABLE**

Academic subjects

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique subject ID |
| `name` | varchar | Subject name (e.g., "Mathematics") |
| `code` | varchar UNIQUE | Subject code (e.g., "MATH101") |
| `school_class_id` | bigint FK | For which class |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## ✅ **8. ATTENDANCES TABLE**

Daily attendance records

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique attendance ID |
| `student_id` | bigint FK | Which student |
| `date` | date | Attendance date |
| `status` | enum | present/absent/late/excused |
| `remarks` | text NULL | Optional notes |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 📊 **9. MARKS TABLE**

Exam marks/grades

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique mark ID |
| `student_id` | bigint FK | Which student |
| `subject_id` | bigint FK | Which subject |
| `exam_type` | varchar | midterm/final/quiz |
| `marks_obtained` | decimal(5,2) | Marks scored |
| `total_marks` | decimal(5,2) | Maximum marks |
| `exam_date` | date | Exam date |
| `remarks` | text NULL | Comments |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 💰 **10. FEES TABLE**

Fee management

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique fee ID |
| `student_id` | bigint FK | Which student |
| `fee_type` | varchar | tuition/exam/library |
| `amount` | decimal(10,2) | Fee amount |
| `due_date` | date | Payment due date |
| `paid_at` | timestamp NULL | Payment date/time |
| `payment_status` | enum | pending/paid/overdue |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 📅 **11. TIMETABLES TABLE**

Class schedules

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | bigint PK | Unique timetable ID |
| `school_class_id` | bigint FK | Which class |
| `section_id` | bigint FK | Which section |
| `subject_id` | bigint FK | Which subject |
| `teacher_id` | bigint FK | Which teacher |
| `day_of_week` | enum | monday-sunday |
| `start_time` | time | Period start time |
| `end_time` | time | Period end time |
| `room_number` | varchar NULL | Classroom number |
| `created_at` | timestamp | Record creation |
| `updated_at` | timestamp | Last update |

---

## 🔧 **12. SESSIONS TABLE**

Laravel session data (for stateful applications)

### Columns:

| Column | Type | Purpose |
|--------|------|---------|
| `id` | varchar PK | Unique session ID |
| `user_id` | bigint FK NULL | Logged in user (NULL for guests) |
| `ip_address` | varchar(45) | User's IP address |
| `user_agent` | text | Browser/device info |
| `payload` | longtext | Serialized session data |
| `last_activity` | integer | Unix timestamp of last activity |

### How Sessions Work:

```php
Purpose: User ka temporary data store karta hai (login state, flash messages, etc.)

Flow:
1. User website visit karta hai
2. Laravel unique session ID generate karta hai
3. Session cookie browser mein store hota hai
4. Session data (cart, login state, etc.) database mein store hota hai
5. Har request par session ID cookie se read karke data restore hota hai

Example Data:
- Login state (authenticated user ID)
- Flash messages ("Student added successfully!")
- Form old input (validation fail hone par)
- Shopping cart items
- CSRF tokens

Cleanup:
// Inactive sessions (>2 hours) clean karne ke liye
php artisan session:gc
```

---

## 📊 **Entity Relationship Diagram (ERD)**

```
USERS (Parent Table)
  ├── hasOne → STUDENT
  ├── hasOne → TEACHER
  └── hasMany → SESSIONS

STUDENT
  ├── belongsTo → USER
  ├── belongsTo → SCHOOL_CLASS
  ├── belongsTo → SECTION
  ├── hasMany → ATTENDANCES
  ├── hasMany → MARKS
  └── hasMany → FEES

TEACHER
  ├── belongsTo → USER
  └── hasMany → TIMETABLES

SCHOOL_CLASS
  ├── hasMany → SECTIONS
  ├── hasMany → SUBJECTS
  ├── hasMany → STUDENTS
  └── hasMany → TIMETABLES

SUBJECT
  ├── belongsTo → SCHOOL_CLASS
  ├── hasMany → MARKS
  └── hasMany → TIMETABLES

TIMETABLE
  ├── belongsTo → SCHOOL_CLASS
  ├── belongsTo → SECTION
  ├── belongsTo → SUBJECT
  └── belongsTo → TEACHER
```

---

## 🔐 **Security Features**

### Password Hashing:
```php
// Passwords kabhi plain text mein store nahi hote
$user->password = Hash::make('secret123'); // Stored as: $2y$10$...
```

### CSRF Protection:
```php
// Forms mein @csrf token required
<form>
    @csrf
    <!-- form fields -->
</form>
```

### SQL Injection Prevention:
```php
// Eloquent ORM automatically sanitize karta hai
Student::where('roll_number', $input)->first(); // Safe!
```

### Remember Token Security:
```php
// Token random generate + hashed storage
// 60 days ke baad expire
// Logout par invalidate
```

---

## 🚀 **Performance Optimization**

### Indexes:
```php
- users.email → UNIQUE index (fast login lookup)
- students.roll_number → UNIQUE index
- students.user_id → FK index
- sessions.last_activity → Index (cleanup ke liye)
```

### Eager Loading:
```php
// N+1 problem avoid karne ke liye
$students = Student::with(['user', 'schoolClass', 'section'])->get();
```

---

## 🧹 **Maintenance Commands**

```bash
# Clear old sessions
php artisan session:gc

# Clear old password reset tokens (custom command needed)
php artisan tinker
DB::table('password_reset_tokens')->where('created_at', '<', now()->subHour())->delete();

# Optimize database
php artisan db:show  # Database stats dekhne ke liye
```

---

## 📝 **Summary**

| Feature | Table | Purpose |
|---------|-------|---------|
| Authentication | users, password_reset_tokens, sessions | Login, password reset, session management |
| User Profiles | users.avatar, students.avatar | Profile pictures |
| Student Info | students | Academic records |
| Teacher Info | teachers | Employee records |
| Academic | school_classes, sections, subjects | Course structure |
| Daily Operations | attendances, marks, fees, timetables | Day-to-day management |

---

**Created:** July 29, 2026  
**Project:** Student Management System (Laravel)  
**Database:** MySQL
