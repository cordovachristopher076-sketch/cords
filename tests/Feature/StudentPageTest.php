<?php

use App\Models\User;

test('admin students page loads', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin User',
    ]);

    $this
        ->actingAs($admin)
        ->get('/admin/students')
        ->assertOk();
});

test('admin can register a student and see them in the student list', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin User',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post('/admin/students', [
            'name' => 'New Student',
            'email' => 'newstudent@example.com',
            'role' => 'student',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

    $response
        ->assertRedirect('/admin/students')
        ->assertSessionHas('status', 'Student registered successfully.');

    $this->assertDatabaseHas('users', [
        'name' => 'New Student',
        'email' => 'newstudent@example.com',
        'role' => 'student',
    ]);

    $this
        ->actingAs($admin)
        ->get('/admin/students')
        ->assertOk()
        ->assertSee('New Student');
});

test('a new public registration is saved as a student', function () {
    $response = $this->post('/register', [
        'name' => 'Public Student',
        'email' => 'publicstudent@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'name' => 'Public Student',
        'email' => 'publicstudent@example.com',
        'role' => 'student',
    ]);
});
