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
        $role = strtolower((string) Session::get('user_type'));
        // Treat both 'admin' and 'event_manager' roles as having admin privileges.
        return in_array($role, ['admin', 'event_manager'], true);
    }
}