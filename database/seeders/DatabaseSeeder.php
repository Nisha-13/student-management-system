<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Create Classes and Sections
        $class10 = SchoolClass::create([
            'name' => 'Grade 10',
            'code' => '10th',
        ]);

        $sectionA = Section::create([
            'school_class_id' => $class10->id,
            'name' => 'Section A',
            'capacity' => 40,
        ]);

        $sectionB = Section::create([
            'school_class_id' => $class10->id,
            'name' => 'Section B',
            'capacity' => 40,
        ]);

        // 3. Create Subjects
        Subject::create([
            'school_class_id' => $class10->id,
            'name' => 'Mathematics',
            'code' => 'MATH10',
        ]);

        Subject::create([
            'school_class_id' => $class10->id,
            'name' => 'Science',
            'code' => 'SCI10',
        ]);

        Subject::create([
            'school_class_id' => $class10->id,
            'name' => 'English',
            'code' => 'ENG10',
        ]);

        // 4. Create Teacher User & Profile
        $teacherUser = User::create([
            'name' => 'Sarah Connor',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_id' => 'TCH-001',
            'phone' => '+1234567890',
            'qualification' => 'M.Sc. Mathematics',
        ]);

        // 5. Create Student User & Profile
        $studentUser = User::create([
            'name' => 'John Doe',
            'email' => 'student@school.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'school_class_id' => $class10->id,
            'section_id' => $sectionA->id,
            'roll_number' => 'STU-1001',
            'dob' => '2008-05-15',
            'gender' => 'male',
            'phone' => '+1987654321',
            'address' => '123 Main Street',
        ]);
    }
}
