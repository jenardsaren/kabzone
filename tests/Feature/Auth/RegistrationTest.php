<?php

test('registration screen is not available to the public', function () {
    $response = $this->get('/register');

    $response->assertNotFound();
});

test('registration submission is blocked to the public', function () {
    $response = $this->post('/register', [
        'first_name' => 'Test',
        'middle_name' => 'Sample',
        'last_name' => 'User',
        'address' => '123 Main Street',
        'contact_no' => '09170000000',
        'gender' => 'male',
        'parent_name' => 'Sample Parent',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNotFound();
    $this->assertGuest();
});
