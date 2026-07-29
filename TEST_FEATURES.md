# 🧪 Complete Feature Testing Guide

## ✅ **COMPLETED UPDATES**

### 1. **Avatar Column Added to Users Table** ✅
- Migration created and run successfully
- User model updated with `avatar` fillable
- `getAvatarUrlAttribute()` accessor added
- Fallback to UI Avatars for users without photos

### 2. **Password Reset System** ✅
- `ForgotPasswordController` created
- `ResetPasswordController` created
- Routes added for password reset flow
- Forgot password view created
- Reset password view created with strength meter
- Login page updated with "Forgot Password" link

### 3. **Email Verification** ✅
- User model implements `MustVerifyEmail` interface
- `email_verified_at` column ready to use

---

## 🔍 **How to Test Each Feature**

### **A) Avatar System**

#### Test Avatar Upload:
```php
// When creating/updating a user/student/teacher:
1. Go to student create form
2. Upload a photo file
3. Submit form
4. Check if photo appears in students list
5. If no photo uploaded, should show UI Avatar with initials
```

#### Test Avatar Fallback:
```php
// Access user avatar URL:
$user = User::find(1);
echo $user->avatar_url;

// With avatar: http://localhost/storage/avatars/user123.jpg
// Without avatar: https://ui-avatars.com/api/?name=Ali+Ahmed&background=3b82f6&color=fff&size=128
```

---

### **B) Password Reset Flow**

#### Step-by-Step Testing:

**Step 1: Request Reset Link**
```
1. Visit: http://localhost/forgot-password
2. Enter admin email (e.g., admin@school.com)
3. Click "Send Reset Link"
4. Check email inbox for reset link
```

**Step 2: Database Check**
```sql
-- Check if token was created:
SELECT * FROM password_reset_tokens WHERE email = 'admin@school.com';

-- You'll see:
-- email: admin@school.com
-- token: $2y$10$... (hashed)
-- created_at: 2026-07-29 10:30:00
```

**Step 3: Reset Password**
```
1. Click the link in email OR visit:
   http://localhost/reset-password/TOKEN_HERE?email=admin@school.com

2. Enter:
   - Email: admin@school.com
   - New Password: NewPassword123!
   - Confirm Password: NewPassword123!

3. Click "Reset Password"

4. Should redirect to login with success message

5. Check database:
   - Token should be DELETED from password_reset_tokens
   - User password should be updated (new hash)
   - remember_token regenerated
```

**Step 4: Token Expiry Test**
```php
// Tokens expire after 60 minutes (config/auth.php)
// To test:
1. Create a reset token
2. Manually update created_at to 61 minutes ago:
   UPDATE password_reset_tokens 
   SET created_at = DATE_SUB(NOW(), INTERVAL 61 MINUTE)
   WHERE email = 'admin@school.com';

3. Try to use the token
4. Should get error: "This password reset token is invalid"
```

---

### **C) Remember Me Functionality**

#### Test Remember Token:

**Step 1: Login with Remember Me**
```
1. Visit: http://localhost/login
2. Enter credentials
3. ✅ CHECK "Remember me" checkbox
4. Click "Sign In"
```

**Step 2: Verify Token Storage**
```sql
-- Check database:
SELECT id, name, remember_token FROM users WHERE email = 'admin@school.com';

-- You'll see a random 60-character token:
-- remember_token: y9xK2...mP8Q (60 chars)
```

**Step 3: Test Auto-Login**
```
1. Close browser completely
2. Reopen and visit: http://localhost
3. Should automatically log you in WITHOUT entering password!
4. Works for 60 days (Laravel default)
```

**Step 4: Check Cookie**
```
Open browser DevTools > Application > Cookies
You'll see:
- Name: remember_web_[hash]
- Value: [user_id]|[token]|[hash]
- Expires: +60 days
```

---

### **D) Email Verification**

#### Test Email Verification Flow:

**Step 1: Create Unverified User**
```php
// When creating a new user:
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@school.com',
    'password' => Hash::make('password'),
    'role' => 'student',
    'email_verified_at' => null, // Unverified
]);
```

**Step 2: Send Verification Email**
```php
// In your controller after user creation:
$user->sendEmailVerificationNotification();

// Or manually in tinker:
php artisan tinker
$user = User::find(1);
$user->sendEmailVerificationNotification();
```

**Step 3: Verify Email**
```
1. User receives email with verification link
2. Click link: http://localhost/email/verify/{id}/{hash}
3. Laravel updates email_verified_at to current timestamp
```

**Step 4: Check Verification Status**
```php
// In your code:
if ($user->hasVerifiedEmail()) {
    // Email is verified
} else {
    // Email not verified
}

// Database check:
SELECT email_verified_at FROM users WHERE id = 1;
-- NULL = not verified
-- 2026-07-29 10:30:00 = verified
```

**Step 5: Protect Routes (Optional)**
```php
// Add 'verified' middleware to routes:
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Unverified users will be redirected to verification notice page
```

---

## 📊 **Database Column Explanations**

### **users.email_verified_at**
```
Type: timestamp NULL
Purpose: Tracks if user's email is verified

Values:
- NULL = Email not verified yet
- Timestamp = Email verified on this date/time

Example Usage:
// Mark as verified:
$user->markEmailAsVerified();

// Check status:
$user->hasVerifiedEmail(); // true/false

// Get verification date:
echo $user->email_verified_at->format('M d, Y'); // "Jul 29, 2026"
```

### **users.remember_token**
```
Type: varchar(100) NULL
Purpose: Stores "Remember Me" authentication token

How it Works:
1. User checks "Remember me" at login
2. Laravel generates 60-char random token
3. Token stored in database + browser cookie
4. Next visit: token matched = auto login
5. Valid for 60 days
6. Logout removes the token

Security:
- Token is random (not password-based)
- Stored in cookie (httpOnly, secure)
- Regenerated on password change
```

### **users.avatar**
```
Type: varchar(255) NULL
Purpose: Stores profile picture file path

Values:
- NULL = No photo uploaded
- "avatars/user_123_1627584926.jpg" = Photo path

Storage Location:
- Physical: storage/app/public/avatars/
- Public access: public/storage/avatars/ (via symlink)
- URL: http://localhost/storage/avatars/user_123.jpg
```

### **password_reset_tokens.email**
```
Type: varchar(255) PRIMARY KEY
Purpose: User's email (unique per reset request)

Note:
- One active token per email
- New request overwrites old token
```

### **password_reset_tokens.token**
```
Type: varchar(255)
Purpose: Hashed password reset token

Security:
- Token is hashed (bcrypt)
- Original token only in email link
- Cannot be reversed from database
```

### **password_reset_tokens.created_at**
```
Type: timestamp
Purpose: Token creation time for expiry check

Expiry:
- Default: 60 minutes (config/auth.php)
- After expiry: token becomes invalid
- Must request new reset link
```

---

## 🔧 **Manual Testing Commands**

### Test Avatar:
```bash
cd "d:\Backend\student-management-system"

# Check if column exists:
php artisan tinker --execute="echo Schema::hasColumn('users', 'avatar') ? 'EXISTS' : 'MISSING';"

# Test avatar accessor:
php artisan tinker
$user = User::first();
echo $user->avatar_url;
exit
```

### Test Remember Token:
```bash
# View remember tokens:
php artisan tinker
User::select('id', 'name', 'remember_token')->take(5)->get();
exit
```

### Test Password Reset Tokens:
```bash
# View active reset tokens:
php artisan tinker
DB::table('password_reset_tokens')->get();
exit

# Clear expired tokens:
php artisan tinker
DB::table('password_reset_tokens')->where('created_at', '<', now()->subHour())->delete();
exit
```

### Test Email Verification:
```bash
# Check verification status:
php artisan tinker
$users = User::select('id', 'name', 'email', 'email_verified_at')->get();
$users->each(fn($u) => dump($u->email . ' => ' . ($u->email_verified_at ? 'Verified' : 'Not Verified')));
exit

# Manually verify a user:
php artisan tinker
$user = User::find(1);
$user->markEmailAsVerified();
echo "User verified!";
exit
```

---

## 🚀 **Complete Flow Examples**

### **New Admin User Registration (with all features):**
```php
// 1. Create user
$user = User::create([
    'name' => 'Admin User',
    'email' => 'admin@school.com',
    'password' => Hash::make('SecurePass123!'),
    'role' => 'admin',
    'avatar' => $avatarPath, // or null
    'email_verified_at' => null, // Will verify via email
]);

// 2. Send verification email
$user->sendEmailVerificationNotification();

// 3. User verifies email (clicks link)
// Laravel automatically updates email_verified_at

// 4. User logs in with "Remember me" checked
// Laravel generates and stores remember_token

// 5. User forgets password
// - Visits /forgot-password
// - Enters email
// - Receives reset link
// - Token stored in password_reset_tokens

// 6. User resets password
// - Clicks link in email
// - Enters new password
// - Password updated
// - Token deleted
// - remember_token regenerated
```

---

## 📋 **Verification Checklist**

### Database Structure: ✅
- [x] users.avatar column exists
- [x] users.email_verified_at column exists
- [x] users.remember_token column exists
- [x] password_reset_tokens table exists
- [x] students.avatar column exists

### Controllers: ✅
- [x] ForgotPasswordController created
- [x] ResetPasswordController created
- [x] LoginController handles remember_token

### Models: ✅
- [x] User model has avatar in fillable
- [x] User model has getAvatarUrlAttribute()
- [x] User implements MustVerifyEmail

### Views: ✅
- [x] forgot-password.blade.php created
- [x] reset-password.blade.php created
- [x] login.blade.php has "Forgot password" link

### Routes: ✅
- [x] /forgot-password routes added
- [x] /reset-password routes added
- [x] Routes are guest-protected

---

## 🎯 **Testing Priority**

1. **HIGH PRIORITY** (Test immediately):
   - Avatar upload/display
   - Login with remember me
   - Forgot password flow

2. **MEDIUM PRIORITY** (Test when relevant):
   - Email verification (if implemented)
   - Token expiry behavior
   - Multiple password resets

3. **LOW PRIORITY** (Test periodically):
   - Old token cleanup
   - Avatar storage cleanup
   - Session management

---

## 📞 **Support & Documentation**

- **Full Documentation**: See `DATABASE_DOCUMENTATION.md`
- **Laravel Docs**: https://laravel.com/docs/11.x
- **Password Reset**: https://laravel.com/docs/11.x/passwords
- **Email Verification**: https://laravel.com/docs/11.x/verification

---

**Last Updated:** July 29, 2026  
**Project:** Student Management System  
**Laravel Version:** 11.x
