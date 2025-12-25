<?php

use Illuminate\Support\Facades\Session;

if (! function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return Session::has('user_id');
    }
}

if (! function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return Session::get('user_type') === 'admin';
    }
}