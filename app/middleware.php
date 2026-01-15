<?php
// Middleware-like checks
function require_auth(): void {
    if (!auth_user()) {
        redirect('r=login');
    }
}

function require_guest(): void {
    if (auth_user()) {
        redirect('r=dashboard');
    }
}