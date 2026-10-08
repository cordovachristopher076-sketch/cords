<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ALAMSS (Automated Laboratory Attendance System)
|--------------------------------------------------------------------------
*/

// 1. Landing Page
Route::get('/', function () {
    return view('welcome');
});

// 2. Guest Routes (Login & Registration)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

// 3. Authenticated Routes
Route::middleware('auth')->group(function () {

    // Student Check-In Route
    Route::post('/student/check-in', [AdminDashboardController::class, 'store'])->name('student.checkin');
    // Logout Action
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // -------------------------------------------------------------
    // AUTOMATIC ROLE-BASED DASHBOARD ROUTE (UPDATED FOR FACULTY/STAFF)
    // -------------------------------------------------------------
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();

        // 1. Tinitingnan kung Admin ang user
        if ((isset($user->role) && $user->role === 'admin') || str_contains(strtolower($user->name), 'admin')) {
            return redirect()->route('admin.dashboard');
        }

        // 2. Tinitingnan kung Faculty o Staff ang user
        if ((isset($user->role) && in_array($user->role, ['faculty', 'staff'])) || str_contains(strtolower($user->name), 'faculty') || str_contains(strtolower($user->name), 'staff')) {
            return redirect()->route('staff.dashboard');
        }

        // 3. Kapag regular student user, ibabato sa student dashboard
        return view('student.dashboard');
    })->name('dashboard');

    // -------------------------------------------------------------
    // STAFF / FACULTY DASHBOARD & ATTENDANCE ACTIONS
    // -------------------------------------------------------------
    Route::prefix('staff')->name('staff.')->group(function () {

        // Staff Dashboard View
        Route::get('/dashboard', function (Request $request) {
            $user = $request->user();
            if (! $user || (! in_array($user->role ?? '', ['faculty', 'staff']) && ! str_contains(strtolower($user->name), 'faculty') && ! str_contains(strtolower($user->name), 'staff') && ($user->role ?? '') !== 'admin')) {
                abort(403, 'THIS ACTION IS UNAUTHORIZED FOR NON-STAFF ACCOUNTS');
            }

            $attendances = Attendance::latest()->get();

            return view('staff.dashboard', compact('attendances'));
        })->name('dashboard');

        // Staff Manual Attendance Actions
        Route::post('/attendance', [AdminDashboardController::class, 'store'])->name('attendance.store');
        Route::put('/attendance/{id}', [AdminDashboardController::class, 'update'])->name('attendance.update');
    });

    // -------------------------------------------------------------
    // ADMIN DASHBOARD & ATTENDANCE CRUD (PROTECTED WITH 403 SECURITY)
    // -------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->group(function () {

        // Inline Security Check inside Group
        Route::get('/dashboard', function (Request $request) {
            $user = $request->user();
            if (! $user || (($user->role ?? '') !== 'admin' && ! str_contains(strtolower($user->name), 'admin'))) {
                abort(403, 'THIS ACTION IS UNAUTHORIZED');
            }

            return app(AdminDashboardController::class)->index();
        })->name('dashboard');

        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

        // DIRECT EXPORT ATTENDANCE SUMMARY (NO CONTROLLER NEEDED)
        Route::get('/attendance/export', function () {
            $fileName = 'attendance_summary_'.date('Y-m-d').'.csv';

            $headers = [
                'Content-type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$fileName",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->stream(function () {
                $handle = fopen('php://output', 'w');

                // CSV Column Headers
                fputcsv($handle, ['Student Name', 'Student ID', 'Course & Section', 'Station', 'Check-In Time', 'Status']);

                // Fetch Attendance Records
                $attendances = Attendance::latest()->get();

                foreach ($attendances as $log) {
                    fputcsv($handle, [
                        $log->name ?? 'N/A',
                        $log->student_id ?? 'N/A',
                        $log->course ?? 'N/A',
                        $log->station ?? 'N/A',
                        $log->created_at ? $log->created_at->format('Y-m-d h:i A') : 'N/A',
                        $log->status ?? 'Active',
                    ]);
                }

                fclose($handle);
            }, 200, $headers);
        })->name('attendance.export');

        // Class Schedule Route
        Route::post('/schedule', [AdminDashboardController::class, 'storeSchedule'])->name('schedule.store');

        // Attendance CRUD Routes
        Route::post('/attendance', [AdminDashboardController::class, 'store'])->name('attendance.store');
        Route::put('/attendance/{id}', [AdminDashboardController::class, 'update'])->name('attendance.update');
        Route::delete('/attendance/{id}', [AdminDashboardController::class, 'destroy'])->name('attendance.destroy');
    });

    // -------------------------------------------------------------
    // USER PROFILE & UTILITIES
    // -------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
