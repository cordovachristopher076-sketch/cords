<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaboratoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Automated Laboratory Attendance & Security System
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC AUTHENTICATION & REGISTRATION
// ==========================================
// Public endpoints for account creation (with role selection) and user login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// 2. PROTECTED API ROUTES (Sanctum Authenticated)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Authentication Actions ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Attendance API Module (Students, Instructors, Admins) ---
    // Digital check-in/check-out and real-time active session retrieval
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/active', [AttendanceController::class, 'activeSessions']);

    // ==========================================
    // 3. ADMINISTRATIVE & INSTRUCTOR RESTRICTED ROUTES (RBAC)
    // ==========================================
    // Restricted using custom role middleware (Admin & Instructor access only)
    Route::middleware('role:admin,instructor')->group(function () {

        // --- Reports & Audit API Module ---
        // Generates aggregated attendance summary reports by course/date/student[cite: 1, 2]
        Route::get('/reports/attendance', [AttendanceController::class, 'generateReport']);

        // --- Laboratory & Station Management API Module ---
        // RESTful resource routes to manage rooms, terminal allocations, and availability[cite: 1, 2]
        Route::apiResource('/laboratories', LaboratoryController::class);

    });
});
