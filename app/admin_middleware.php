<?php
// Admin middleware-like checks
function require_admin_auth(): void {
    if (!admin_user()) {
        redirect('?r=login');
    }
}

function require_admin_guest(): void {
    if (admin_user()) {
        redirect('?r=dashboard');
    }
}
