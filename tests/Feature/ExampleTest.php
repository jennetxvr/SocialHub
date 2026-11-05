<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated visitors are redirected to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('public explore page returns a successful response', function () {
    $response = $this->get('/explore');

    $response->assertStatus(200);
});
