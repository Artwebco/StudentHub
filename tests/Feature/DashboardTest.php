<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('admins can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create(['role' => 'admin']));

    $this->get('/dashboard')->assertStatus(200);
});

test('non-admin users cannot visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create(['role' => 'student']));

    $this->get('/dashboard')->assertStatus(403);
});
