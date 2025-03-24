<?php
$input = 'admin123'; 
$storedHash = '$2y$10$kP4KZ63ILxJOcerdR0qRfe2WWzy7tXPnJSyno1DsW/TyrcVq4EQli';

if (password_verify($input, $storedHash)) {
    echo "Password matches!";
} else {
    echo "Password does NOT match!";
}
